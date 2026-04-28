<?php

namespace App\Entity;

use App\Repository\NewsletterSubscriberRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NewsletterSubscriberRepository::class)]
class NewsletterSubscriber
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank()]
    #[Assert\Email()]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $subscribedAt = null;

    public function __construct()
    {
        $this->subscribedAt = new \DateTimeImmutable('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getSubscribedAt(): ?\DateTimeImmutable
    {
        return $this->subscribedAt;
    }

    public function setSubscribedAt(\DateTimeImmutable $subscribedAt): static
    {
        $this->subscribedAt = $subscribedAt;

        return $this;
    }
}
