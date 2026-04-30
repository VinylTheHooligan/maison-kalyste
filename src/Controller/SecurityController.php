<?php

namespace App\Controller;

use App\DTO\User\RegistrationDTO;
use App\DTO\User\ResendActivationDTO;
use App\Form\RegistrationFormType;
use App\Form\ResendActivationFormType;
use App\Repository\UserRepository;
use App\Services\ActivationEmailService;
use App\Services\AssemblerDTOService;
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
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

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

            if (!$user->isVerified())
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
}
