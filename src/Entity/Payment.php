<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $stripe_payment_intent_id = null;

    #[ORM\Column]
    private ?int $amout = null;

    #[ORM\Column(length: 255)]
    private ?string $currency = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $paid_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStripePaymentIntentId(): ?string
    {
        return $this->stripe_payment_intent_id;
    }

    public function setStripePaymentIntentId(string $stripe_payment_intent_id): static
    {
        $this->stripe_payment_intent_id = $stripe_payment_intent_id;

        return $this;
    }

    public function getAmout(): ?int
    {
        return $this->amout;
    }

    public function setAmout(int $amout): static
    {
        $this->amout = $amout;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPaidAt(): ?\DateTimeImmutable
    {
        return $this->paid_at;
    }

    public function setPaidAt(\DateTimeImmutable $paid_at): static
    {
        $this->paid_at = $paid_at;

        return $this;
    }
}
