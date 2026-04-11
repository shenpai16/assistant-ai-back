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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class StripeController extends AbstractController
{
    private string $stripeSecretKey;

    public function __construct(string $stripeSecretKey)
    {
        $this->stripeSecretKey = $stripeSecretKey;
    }

    private array $prices = [
        'starter' => 'price_1TL3DXIKELX53FhQct72F213',
        'business' => 'price_1TL3DnIKELX53FhQ7ykrsmG5',
        'enterprise' => 'price_1TL3E0IKELX53FhQcyIC0wE1',
    ];

    #[Route('/create-checkout-session/{plan}', name: 'app_create_checkout_session')]
    public function createCheckoutSession(
        StripeService $stripeService,
        EntityManagerInterface $em,
        string $plan
    ): JsonResponse
    {
        try {   
            $user = $this->getUser();
            
            if (!$user) {
                return new JsonResponse(['error' => 'User not authenticated'], 401);
            }

            if (!isset($this->prices[$plan])) {
                return new JsonResponse(['error' => 'Invalid plan selected'], 400);
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
                    'price' => $this->prices[$plan],
                    'quantity' => 1,
                ]],
                'success_url' => $this->generateUrl('app_stripe_success', [
                    'session_id' => '{CHECKOUT_SESSION_ID}',
                ], UrlGeneratorInterface::ABSOLUTE_URL),

                'cancel_url' => $this->generateUrl('app_stripe_cancel', [
                    'session_id' => '{CHECKOUT_SESSION_ID}',
                ], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);

            return new JsonResponse(['sessionId' => $session->id]);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/success', name: 'app_stripe_success')]
    public function success(Request $request): Response
    {
        $sessionId = $request->query->get('session_id');

        if (!$sessionId) {
            return new Response('Session ID is missing', 400);
        }

        return $this->render('stripe/success.html.twig', [
            'sessionId' => $sessionId,
        ]);
    }

    #[Route('/cancel', name: 'app_stripe_cancel')]
    public function cancel(Request $request): Response
    {
        $sessionId = $request->query->get('session_id');

        if (!$sessionId) {
            return new Response('Session ID is missing', 400);
        }

        return $this->render('stripe/cancel.html.twig', [
            'sessionId' => $sessionId,
        ]);
    }
}