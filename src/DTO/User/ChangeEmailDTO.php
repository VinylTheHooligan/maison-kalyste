<?php

namespace App\DTO\User;

use Symfony\Component\Validator\Constraints as Assert;

class ChangeEmailDTO
{
    #[Assert\NotBlank(message: 'Veuillez saisir votre ancien e-mail.', groups: ['email_change'])]
    #[Assert\Email]
    public ?string $oldEmail = null;

    #[Assert\NotBlank(message: 'Veuillez saisir un nouveau mot de passe.', groups: ['email_change'])]
    #[Assert\Email]
    public ?string $newEmail = null;
}