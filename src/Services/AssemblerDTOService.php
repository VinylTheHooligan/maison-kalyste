<?php

namespace App\Services;

use App\DTO\User\RegistrationDTO;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AssemblerDTOService {
    public function __construct(
        private UserPasswordHasherInterface $hasher,
    )
    {}

    public function fromRegistrationDTO(RegistrationDTO $dto): User
    {
        $user = new User();

        $user->setEmail($dto->email);
        $user->setFirstName($dto->firstName);
        $user->setLastName($dto->lastName);
        $user->setRoles(['ROLE_USER']);

        $hashed = $this->hasher->hashPassword($user, $dto->plainPassword);
        $user->setPassword($hashed);

        $user->setIsVerified(false);
        $user->setActivationToken(bin2hex(random_bytes(32)));

        return $user;
    }
}
