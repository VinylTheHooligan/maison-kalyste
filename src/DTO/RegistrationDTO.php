<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RegistrationDTO
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100)]
    public ?string $firstName = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100)]
    public ?string $lastName = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    public ?string $plainPassword = null;

    #[Assert\IsTrue(message: 'Vous devez accepter les conditions.')]
    public bool $agreeTerms = false;
}