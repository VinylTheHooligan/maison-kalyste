<?php

namespace App\Services;

use App\DTO\User\RegistrationDTO;
use App\DTO\User\ResetPasswordDTO;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AssemblerDTOService {
    public function __construct(
        private UserPasswordHasherInterface $hasher,
    )
    {}

    public function fromRegistrationDTO(RegistrationDTO $dto, string $token): User
    {
        $user = new User();

        $user->setEmail($dto->email);
        $user->setFirstName($dto->firstName);
        $user->setLastName($dto->lastName);
        $user->setUsername($dto->username);
        $user->setRoles(['ROLE_USER']);

        $hashed = $this->hasher->hashPassword($user, $dto->plainPassword);
        $user->setPassword($hashed);

        $user->setIsVerified(false);

        $user->setActivationToken(hash('sha256', $token));
        return $user;
    }

    public function updatePasswordFromDTO(User $user, ResetPasswordDTO $dto): User
    {
        $hashedPassword = $this->hasher->hashPassword($user, $dto->plainPassword);

        $user->setPassword($hashedPassword);
        $user->setUpdatedAt(new \DateTimeImmutable());

        return $user;
    }
}
