<?php

namespace App\Controller\Abonnement;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
{
    #[Route('/admin/abonnements', name: 'admin_subscriptions')]
    public function subscriptions(): Response
    {
        return $this->render('subscriptions/index.html.twig');
    }

}
