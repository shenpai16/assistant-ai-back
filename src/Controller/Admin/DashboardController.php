<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use App\Entity\Conversation;
use App\Entity\Message;
use App\Controller\Admin\CompanyCrudController;
use App\Controller\Admin\ConversationCrudController;
use App\Controller\Admin\MessageCrudController;

use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;


#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
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

        return $this->render('admin/dashboard.html.twig', [
            'companyCount' => $companyCount,
            'conversationCount' => $conversationCount,
            'messageCount' => $messageCount,
            'messageLast24h' => $messageLast24h,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Assistant IA Back');
    }

     public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),

            MenuItem::section('Entreprises'),
            MenuItem::linkTo(CompanyCrudController::class, 'Entreprises', 'fa fa-building'),
            MenuItem::linkTo(ConversationCrudController::class, 'Conversations', 'fa fa-comments'),
            MenuItem::linkTo(MessageCrudController::class, 'Messages', 'fa fa-envelope'),
        ];
    }
}