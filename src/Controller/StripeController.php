<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\Stripe\StripeService;
use Symfony\Component\HttpFoundation\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\StripeClient;
use Stripe\Webhook;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class StripeController extends AbstractController
{
    private string $stripeSecretKey;

    public function __construct(string $stripeSecretKey)
    {
        $this->stripeSecretKey = $stripeSecretKey;
    }

    #[Route('/create-checkout-session', name: 'app_create_checkout_session')]
    public function createCheckoutSession(
        StripeService $stripeService,
        EntityManagerInterface $em
    ): JsonResponse
    {
        try {   
            $user = $this->getUser();
            
            if (!$user) {
                return new JsonResponse(['error' => 'User not authenticated'], 401);
            }

            if (!$user->getStripeCustomerId()) {
                $customerId = $stripeService->createCustomer($user);
                $user->setStripeCustomerId($customerId);

                $em->persist($user);
                $em->flush();
            }

            $stripe = new StripeClient($this->stripeSecretKey);

            $session = $stripe->checkout->sessions->create([
                'mode' => 'subscription',
                'customer' => $user->getStripeCustomerId(),
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => 'price_1TGHAKIKELX53FhQMqbqmxYW',
                    'quantity' => 1,
                ]],
                'success_url' => 'http://localhost:8000/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => 'http://localhost:8000/cancel',
            ]);

            return new JsonResponse(['sessionId' => $session->id]);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}