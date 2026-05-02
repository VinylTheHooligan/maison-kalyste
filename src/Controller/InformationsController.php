<?php

namespace App\Controller;

use App\Entity\ContactMessage;
use App\Form\ContactMessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\RateLimiter\RateLimiterFactory;

#[Route('/informations', name: 'app_informations')]
final class InformationsController extends AbstractController
{
    #[Route('/legal', name: '_legal')]
    public function legal(): Response
    {
        return $this->render('informations/legal.html.twig', [
            'controller_name' => 'InformationsController',
        ]);
    }

    #[Route('/cgu', name: '_cgu')]
    public function cgu(): Response
    {
        return $this->render('informations/cgu.html.twig', [
            'controller_name' => 'InformationsController',
        ]);
    }

    #[Route('/cgv', name: '_cgv')]
    public function cgv(): Response
    {
        return $this->render('informations/cgv.html.twig', [
            'controller_name' => 'InformationsController',
        ]);
    }

    #[Route('/privacy', name: '_privacy')]
    public function privacy(): Response
    {
        return $this->render('informations/privacy.html.twig', [
            'controller_name' => 'InformationsController',
        ]);
    }

    #[Route('/returns', name: '_returns')]
    public function returns(): Response
    {
        return $this->render('informations/returns.html.twig', [
            'controller_name' => 'InformationsController',
        ]);
    }

    #[Route('/delivery', name: '_delivery')]
    public function delivery(): Response
    {
        return $this->render('informations/delivery.html.twig', [
            'controller_name' => 'InformationsController',
        ]);
    }

    #[Route('/contact', name: '_contact')]
    public function contact(Request $request, EntityManagerInterface $em, RateLimiterFactory $contactFormLimiter): Response
    {
        $limiter = $contactFormLimiter->create($request->getClientIp());
        if (!$limiter->consume(1)->isAccepted())
        {
            throw new TooManyRequestsHttpException(null, 'Trop de tentatives, réessayez plus tard.');
        }

        $contact = new ContactMessage();
        
        $form = $this->createForm(ContactMessageType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted())
        {
            // honeypot à bot                                                                   
            if ($form->has('website') && $form->get('website')->getData())
            {
                return $this->redirectToRoute('app_home');
            }

            if ($form->isValid())
            {
                $em->persist($contact);
                $em->flush();

                $this->addFlash('success', 'Votre message à bien été soumis !');
                return $this->redirectToRoute('app_home');
            }
        }

        return $this->render('informations/contact.html.twig', [
            'controller_name' => 'InformationsController',
            'contact_form' => $form,
        ]);
    }
}
