<?php

namespace App\DTO\User;

use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 12)]
    public ?string $plainPassword = null;
}