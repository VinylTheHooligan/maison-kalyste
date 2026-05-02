<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordDTO
{
    #[Assert\NotBlank(message: 'Veuillez saisir votre ancien mot de passe.')]
    public string $oldPassword = '';

    #[Assert\NotBlank(message: 'Veuillez saisir un nouveau mot de passe.')]
    #[Assert\Length(min: 8, minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.')]
    public string $plainPassword = '';
}