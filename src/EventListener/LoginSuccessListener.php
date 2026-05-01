<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginSuccessListener {
    public function __construct(
        private EntityManagerInterface $em,
    )
    {}

    public function __invoke(LoginSuccessEvent $event)
    {
        $user = $event->getUser();

        /** @var User $user */
        if (!$user instanceof User) {
            return;
        }

        $user->setLastLoginAt(new \DateTimeImmutable());

        $this->em->flush();
    }
}