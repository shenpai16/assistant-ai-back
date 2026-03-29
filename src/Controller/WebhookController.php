<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;


final class WebhookController extends AbstractController
{
    #[Route('/webhook', name: 'app_webhook')]
    public function index(): Response
    {
        return $this->render('webhook/index.html.twig', [
            'controller_name' => 'WebhookController',
        ]);
    }

    #[Route('/webhook/stripe', name: 'app_webhook_stripe', methods: ['POST'])]
    public function stripeWebhook(Request $request): Response
    {
        $payload = $request->getContent();
        $sig = $request->headers->get('stripe-signature');
        $secret = $_ENV['STRIPE_WEBHOOK_SECRET'];

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig, $secret);
        }catch (\Exception $e) {
            return new Response('Invalid payload', Response::HTTP_BAD_REQUEST);
        }

        file_put_contents('stripe.log', $event->type.PHP_EOL, FILE_APPEND);
        
        return new Response('Webhook received', Response::HTTP_OK);
    }
}
