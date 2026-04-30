<?php

namespace App\DTO\User;

use Symfony\Component\Validator\Constraints as Assert;

class ResendActivationDTO
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;
}