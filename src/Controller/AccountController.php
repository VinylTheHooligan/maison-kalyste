<?php

namespace App\Controller;

use App\DTO\ChangePasswordDTO;
use App\Entity\User;
use App\Form\User\AccountInfo\NewPasswordFormType;
use App\Form\User\AccountInfo\NameUsernameFormType;
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
    public function info(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $formNameUser = $this->createForm(NameUsernameFormType::class, $user);
        $formNameUser->handleRequest($request);

        $dto = new ChangePasswordDTO();
        $formPassword = $this->createForm(NewPasswordFormType::class, $dto);
        $formPassword->handleRequest($request);

        if ($formNameUser->isSubmitted() && $formNameUser->isValid())
        {
            $user->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();
            $this->addFlash('success', 'Informations de votre compte mis à jour.');
            return $this->redirectToRoute('app_account_info');
        }

        if ($formPassword->isSubmitted() && $formPassword->isValid())
        {
            if (!$hasher->isPasswordValid($user, $dto->oldPassword))
            {
                $this->addFlash('error', 'Ancien mot de passe incorrect.');
                return $this->redirectToRoute('app_account_info');
            }

            $user->setPassword($hasher->hashPassword($user, $dto->plainPassword));
            $user->setUpdatedAt(new \DateTimeImmutable());

            $em->flush();
            $this->addFlash('success', 'Votre mot de passe à bien été mis à jour.');
            return $this->redirectToRoute('app_account_info');
        }

        return $this->render('account/info.html.twig', [
            'formNameUser' => $formNameUser->createView(),
            'formPassword' => $formPassword->createView(),
            'controller_name' => 'accountController'
        ]);
    }
}