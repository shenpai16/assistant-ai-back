<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
class Subscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'subscription')]
    private Collection $user_id;

    #[ORM\Column(length: 255)]
    private ?string $strip_subscription_id = null;

    #[ORM\Column(length: 255)]
    private ?string $strip_price_id = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTime $current_period_end = null;

    #[ORM\Column]
    private ?bool $cancel_at_period_end = null;

    public function __construct()
    {
        $this->user_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUserId(): Collection
    {
        return $this->user_id;
    }

    public function addUserId(User $userId): static
    {
        if (!$this->user_id->contains($userId)) {
            $this->user_id->add($userId);
            $userId->setSubscription($this);
        }

        return $this;
    }

    public function removeUserId(User $userId): static
    {
        if ($this->user_id->removeElement($userId)) {
            // set the owning side to null (unless already changed)
            if ($userId->getSubscription() === $this) {
                $userId->setSubscription(null);
            }
        }

        return $this;
    }

    public function getStripSubscriptionId(): ?string
    {
        return $this->strip_subscription_id;
    }

    public function setStripSubscriptionId(string $strip_subscription_id): static
    {
        $this->strip_subscription_id = $strip_subscription_id;

        return $this;
    }

    public function getStripPriceId(): ?string
    {
        return $this->strip_price_id;
    }

    public function setStripPriceId(string $strip_price_id): static
    {
        $this->strip_price_id = $strip_price_id;

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

    public function getCurrentPeriodEnd(): ?\DateTime
    {
        return $this->current_period_end;
    }

    public function setCurrentPeriodEnd(\DateTime $current_period_end): static
    {
        $this->current_period_end = $current_period_end;

        return $this;
    }

    public function isCancelAtPeriodEnd(): ?bool
    {
        return $this->cancel_at_period_end;
    }

    public function setCancelAtPeriodEnd(bool $cancel_at_period_end): static
    {
        $this->cancel_at_period_end = $cancel_at_period_end;

        return $this;
    }
}
