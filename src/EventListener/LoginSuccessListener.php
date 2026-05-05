<?php

namespace App\EventListener;

use App\Entity\User;
use App\Services\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

#[AsEventListener(event: LoginSuccessEvent::class)]
class LoginSuccessListener
{
    public function __construct(
        private EntityManagerInterface $em,
        private CartService $cartService,
    ) {}

    public function __invoke(LoginSuccessEvent $event): void
    {
        /** @var User $passportUser */
        $passportUser = $event->getPassport()?->getUser();
        $user = $this->em->getRepository(User::class)->find($passportUser->getId());

        if ($user === null) {
            return;
        }

        $user->setLastLoginAt(new \DateTimeImmutable());
        $this->em->flush();

        // ← ajoute juste cette ligne
        $this->cartService->mergeGuestCartOnLogin($user);
    }
}