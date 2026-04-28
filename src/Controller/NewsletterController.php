<?php

namespace App\Controller;

use App\Entity\NewsletterSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/newsletter', name: 'app_newsletter')]
final class NewsletterController extends AbstractController
{
    #[Route('/subscribe', name: '_subscribe')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $errorMessage = 'Veuillez entrer un mail valide.';

        $email = $request->request->get('email');

        if (!$email)
        {
            $this->addFlash(
               'error',
               $errorMessage
            );
            return $this->redirectToRoute('app_home');
        }

        $existing = $em->getRepository(NewsletterSubscriber::class)
                       ->findOneBy(['email' => $email]);

        if ($existing)
        {
            $this->addFlash(
               'error',
               $errorMessage
            );
            return $this->redirectToRoute('app_home');
        }

        $subscriber = new NewsletterSubscriber();
        $subscriber->setEmail($email);

        $em->persist($subscriber);
        $em->flush();

        $this->addFlash('success', 'Merci ! Vous êtes maintenant inscrit à la newsletter.');

        return $this->redirectToRoute('app_home');
    }
}
