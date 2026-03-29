<?php

namespace App\Service\Stripe;

use Stripe\Stripe;
use Stripe\Customer;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\StripeClient;

class StripeService
{
    public function __construct(private EntityManagerInterface $entityManager, private string $stripeSecretKey)
    {
        $this->stripe = new StripeClient($this->stripeSecretKey);
    }

    public function createCustomer(User $user): string
    {
        $customer = $this->stripe->customers->create([
            'email' => $user->getEmail(),
        ]);

        // You can save the Stripe customer ID in your database if needed
        $user->setStripeCustomerId($customer->id);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $customer->id;
    }
}