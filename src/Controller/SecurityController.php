<?php

namespace App\Controller;

use App\DTO\User\ForgotPasswordDTO;
use App\DTO\User\RegistrationDTO;
use App\DTO\User\ResendActivationDTO;
use App\DTO\User\ResetPasswordDTO;
use App\Entity\User;
use App\Form\ForgotPasswordFormType;
use App\Form\RegistrationFormType;
use App\Form\ResendActivationFormType;
use App\Form\ResetPasswordFormType;
use App\Repository\UserRepository;
use App\Services\ActivationEmailService;
use App\Services\AssemblerDTOService;
use App\Services\ResetPasswordEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($error)
        {
            $this->addFlash('error', $error->getMessageKey());
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/register', name: 'app_register')]
    public function register(
        Request $request, 
        AssemblerDTOService $assembler, 
        EntityManagerInterface $em, 
        ActivationEmailService $activationEmailService
    ): Response
    {
        $dto = new RegistrationDTO();

        $form = $this->createForm(RegistrationFormType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $dto->plainPassword = $form->get('plainPassword')->getData();
            $dto->agreeTerms = $form->get('agreeTerms')->getData();

            $user = $assembler->fromRegistrationDTO($dto);

            $user->setActivationExpiresAt(
                new \DateTimeImmutable('+24 hours')
            );

            $em->persist($user);
            $em->flush();

            $activationEmailService->sendActivationEmail($user);

            $this->addFlash('success', "Votre compte a bien été créé. Vérifiez vos emails pour l'activer.");

            return $this->redirectToRoute('app_login');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->render('security/register.html.twig', [
                'form' => $form->createView(),
            ], new Response(status: 422));
        }


        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/activate/{token}', name: 'app_activate_account')]
    public function activate(string $token, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $user = $userRepository->findOneBy(['activationToken' => $token]);

        if (!$user)
        {
            $this->addFlash('danger', "Lien d'activation invalide ou expiré.");
            return $this->redirectToRoute('app_login');
        }

        if ($user->getActivationExpiresAt() < new \DateTimeImmutable())
        {
            $this->addFlash('danger', "Le lien d'activation a expiré.");
            return $this->redirectToRoute('app_resend_activation');
        }

        $user->setIsVerified(true);
        $user->setActivationToken(null);
        $user->setUpdatedAt(new \DateTimeImmutable('now'));

        $em->flush();

        $this->addFlash('success', "Votre compte est maintenant activé. Vous pouvez vous connecter.");

        return $this->redirectToRoute('app_login');
    }

    #[Route('/resend-activation', name: 'app_resend_activation')]
    public function resend(
        Request $request,
        UserRepository $userRepository,
        ActivationEmailService $activationEmailService,
        EntityManagerInterface $em
    ): Response
    {
        $dto = new ResendActivationDTO();

        $form = $this->createForm(ResendActivationFormType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $user = $userRepository->findOneBy(['email' => $dto->email]);

            if (!$user)
            {
                $this->addFlash('danger', 'Aucun compte trouvé avec cet email.');
                return $this->redirectToRoute('app_resend_activation');
            }

            if ($user->isVerified())
            {
                $this->addFlash('info', 'Ce compte est déjà activé.');
                return $this->redirectToRoute('app_login');
            }

            $user->setActivationToken(bin2hex(random_bytes(32)));
            $user->setActivationExpiresAt(new \DateTimeImmutable('+24 hours'));

            $em->flush();

            $activationEmailService->sendActivationEmail($user);

            $this->addFlash('success', "Un nouvel email d'activation vous a été envoyé.");
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/resend.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgot(
        Request $request,
        UserRepository $userRepository,
        ResetPasswordEmailService $resetPasswordEmailService,
        EntityManagerInterface $em
    ): Response
    {
        $dto = new ForgotPasswordDTO();

        $form = $this->createForm(ForgotPasswordFormType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $user = $userRepository->findOneBy(['email' => $dto->email]);

            if ($user)
            {
                $user->setResetPasswordToken(bin2hex(random_bytes(32)));
                $user->setResetPasswordExpiresAt(new \DateTimeImmutable('+1 hour'));
                $em->flush();

                $resetPasswordEmailService->sendResetEmail($user);
            }

            $this->addFlash('success', 'Si un compte existe, un email a été envoyé.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/forgot_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function reset(
        string $token,
        Request $request,
        UserRepository $userRepository,
        AssemblerDTOService $assembler,
        EntityManagerInterface $em
    ): Response
    {
        $user = $userRepository->findOneBy(['resetPasswordToken' => $token]);

        if (!$user || $user->getResetPasswordExpiresAt() < new \DateTimeImmutable())
        {
            $this->addFlash('danger', 'Lien invalide ou expiré.');
            return $this->redirectToRoute('app_forgot_password');
        }

        $dto = new ResetPasswordDTO();

        $form = $this->createForm(ResetPasswordFormType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $assembler->updatePasswordFromDTO($user, $dto);

            $user->setResetPasswordToken(null);
            $user->setResetPasswordExpiresAt(null);

            $em->flush();

            $this->addFlash('success', 'Votre mot de passe a bien été réinitialisé.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('security/reset_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
