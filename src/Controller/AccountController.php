<?php

namespace App\Controller;

use App\DTO\User\ChangeEmailDTO;
use App\DTO\User\ChangePasswordDTO;
use App\Entity\User;
use App\Form\User\AccountInfo\NewPasswordFormType;
use App\Form\User\AccountInfo\NameUsernameFormType;
use App\Form\User\AccountInfo\NewEmailFormType;
use App\Services\ActivationEmailService;
use App\Services\ResetPasswordEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/account', name: 'app_account')]
final class AccountController extends AbstractController
{
    #[Route('/', name: '_my')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('account/myaccount.html.twig', [
            'controller_name' => 'accountController',
        ]);
    }

    #[Route('/orders', name: '_orders')]
    public function order(): Response
    {
        

        return $this->render('account/order.html.twig', [
            'controller_name' => 'accountController',
        ]);
    }

    #[Route('/adresses', name: '_addresses')]
    public function address(): Response
    {
        return $this->render('account/address.html.twig', [
            'controller_name' => 'accountController'
        ]);
    }

    #[Route('/payment', name: '_payments')]
    public function payment(): Response
    {
        return $this->render('account/payment.html.twig', [
            'controller_name' => 'accountController'
        ]);
    }

    #[Route('/info', name: '_info')]
    public function info(
        Request $request, 
        EntityManagerInterface $em, 
        UserPasswordHasherInterface $hasher,
        ActivationEmailService $activationEmailService,
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $formNameUser = $this->createForm(NameUsernameFormType::class, $user);
        $formNameUser->handleRequest($request);

        $dtoPassword = new ChangePasswordDTO();
        $formPassword = $this->createForm(NewPasswordFormType::class, $dtoPassword);
        $formPassword->handleRequest($request);

        $dtoEmail = new ChangeEmailDTO();
        $formEmail = $this->createForm(NewEmailFormType::class, $dtoEmail);
        $formEmail->handleRequest($request);

        if ($formNameUser->isSubmitted() && $formNameUser->isValid())
        {
            $user->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();
            $this->addFlash('success', 'Informations de votre compte mis à jour.');
            return $this->redirectToRoute('app_account_info');
        }

        if ($formPassword->isSubmitted() && $formPassword->isValid())
        {
            if (!$hasher->isPasswordValid($user, $dtoPassword->oldPassword))
            {
                $this->addFlash('error', 'Ancien mot de passe incorrect.');
                return $this->redirectToRoute('app_account_info');
            }

            $user->setPassword($hasher->hashPassword($user, $dtoPassword->plainPassword));
            $user->setUpdatedAt(new \DateTimeImmutable());

            $em->flush();
            $this->addFlash('success', 'Votre mot de passe à bien été mis à jour.');
            return $this->redirectToRoute('app_account_info');
        }

        if ($formEmail->isSubmitted() && $formEmail->isValid())
        {
            if (trim(strtolower($dtoEmail->oldEmail)) !== trim(strtolower($user->getEmail()))) {
                $this->addFlash('error', 'Ancien e-mail incorrect.');
                return $this->redirectToRoute('app_account_info');
            }

            $token = bin2hex(random_bytes(32));
            $hashedToken = hash('sha256', $token);

            $user->setActivationToken($hashedToken);
            $user->setActivationExpiresAt(new \DateTimeImmutable('+24 hours'));
            
            $em->flush();

            $activationEmailService->sendActivationEmail($user, $token, false);

            $this->addFlash('success', 'Un mail de confirmation a été envoyé sur le nouvel e-mail afin de le validé.');
            return $this->redirectToRoute('app_account_info');
        }

        return $this->render('account/info.html.twig', [
            'formNameUser' => $formNameUser,
            'formPassword' => $formPassword,
            'formEmail' => $formEmail,
            'controller_name' => 'accountController'
        ], new Response(
            status: $formNameUser->isSubmitted() 
        || $formPassword->isSubmitted() 
        || $formEmail->isSubmitted() 
            ? 422 : 200));
    }
}