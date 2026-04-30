<?php

namespace App\Services;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ActivationEmailService {
    public function __construct(
        private MailerInterface $mailer,
        private UrlGeneratorInterface $urlGenerator,
    )
    {}

    public function sendActivationEmail(User $user): void
    {
        $activationUrl = $this->urlGenerator->generate(
            'app_activate_account',
            ['token' => $user->getActivationToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $email = (new TemplatedEmail())
            ->from('no-reply@maisonkalyste.fr')
            ->to($user->getEmail())
            ->subject($user->getFirstName() . ', activez votre compte ! - Maison Kalyste')
            ->htmlTemplate('emails/activation.html.twig')
            ->context([
                'user' => $user,
                'activationUrl' => $activationUrl,
            ])
        ;

        $this->mailer->send($email);
    }
}