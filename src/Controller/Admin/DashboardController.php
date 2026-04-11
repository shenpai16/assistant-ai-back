<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use App\Entity\Conversation;
use App\Entity\Message;
use App\Controller\Admin\CompanyCrudController;
use App\Controller\Admin\ConversationCrudController;
use App\Controller\Admin\MessageCrudController;
use App\Controller\Admin\UserCrudController;


use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use Symfony\Component\Routing\Attribute\Route;


#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addHtmlContentToHead($this->renderView('admin/_ea_theme.html.twig'));
       
    }

    public function index(): Response
    {
        $companyRepo = $this->doctrine->getRepository(Company::class);
        $conversationRepo = $this->doctrine->getRepository(Conversation::class);
        $messageRepo = $this->doctrine->getRepository(Message::class);

        $companyCount = $companyRepo->count([]);
        $conversationCount = $conversationRepo->count([]);
        $messageCount = $messageRepo->count([]);

        $messageLast24h = $messageRepo->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.createdAt >= :date')
            ->setParameter('date', new \DateTimeImmutable('-24 hours'))
            ->getQuery()
            ->getSingleScalarResult();

        if ($this->isGranted('ROLE_CLIENT')){
            return $this->render('admin/dashboard.html.twig', [
                'companyCount' => $companyCount,
                'stripe_public_key' => $_ENV['STRIPE_PUBLIC_KEY'],
            ]);
        }

        
        return $this->render('admin/dashboard.html.twig', [
            'companyCount' => $companyCount,
            'conversationCount' => $conversationCount,
            'messageCount' => $messageCount,
            'messageLast24h' => $messageLast24h,
            'stripe_public_key' => $_ENV['STRIPE_PUBLIC_KEY'],
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Assistant IA Back')
            ->renderContentMaximized()
            ->disableDarkMode();
    }


    #[Route('/admin/abonnements', name: 'admin_subscriptions')]
    public function abonnements(): Response
    {
        return $this->render('subscriptions/index.html.twig', [
            'stripe_public_key' => $_ENV['STRIPE_PUBLIC_KEY'],
        ]);
    }

    

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),

            MenuItem::section('Entreprises'),
            MenuItem::linkTo(CompanyCrudController::class, 'Entreprises', 'fa fa-building'),
            MenuItem::linkTo(ConversationCrudController::class, 'Conversations', 'fa fa-comments'),
            MenuItem::linkTo(MessageCrudController::class, 'Messages', 'fa fa-envelope'),
            MenuItem::linkTo(UserCrudController::class, 'Utilisateurs', 'fa fa-users'),
            MenuItem::linkToRoute('Abonnements', 'fa fa-credit-card', 'admin_subscriptions'),




        ];
    }
}