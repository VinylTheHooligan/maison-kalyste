<?php

namespace App\Services;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker {
    public function checkPreAuth(UserInterface $user): void
    {
        if ($user instanceof User)
        {
            return;
        }

        /** @var User $user */
        if (!$user->isVerified())
        {
            throw new CustomUserMessageAuthenticationException(
                "Votre compte n'est pas encore activé."
            );
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {

    }
}