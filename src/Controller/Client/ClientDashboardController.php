<?php

namespace App\Controller\Client;

use App\Entity\Conversation;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[AdminDashboard(
    routePath: '/client',
    routeName: 'client_dashboard'
)]
class ClientDashboardController extends AbstractDashboardController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/client/dashboard', name: 'client_home')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');

        $company = $this->getUser()->getCompany();

        // Nombre de conversations
        $conversationsCount = $this->em->getRepository(Conversation::class)
            ->count(['company' => $company]);

        // Nombre total de messages
        $messagesCount = $this->em->getRepository(Message::class)
            ->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->join('m.conversation', 'c')
            ->where('c.company = :company')
            ->setParameter('company', $company)
            ->getQuery()
            ->getSingleScalarResult();

        // Messages des dernières 24h
        $messagesLast24h = $this->em->getRepository(Message::class)
            ->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->join('m.conversation', 'c')
            ->where('c.company = :company')
            ->andWhere('m.createdAt >= :date')
            ->setParameter('company', $company)
            ->setParameter('date', new \DateTimeImmutable('-24 hours'))
            ->getQuery()
            ->getSingleScalarResult();

        // Dernières conversations
        $lastConversations = $this->em->getRepository(Conversation::class)
            ->createQueryBuilder('c')
            ->where('c.company = :company')
            ->setParameter('company', $company)
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        // Graphique : messages par jour (7 derniers jours)
        $chartLabels = [];
        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = (new \DateTimeImmutable())->modify("-$i days");
            $nextDay = $day->modify('+1 day');

            $count = $this->em->getRepository(Message::class)
                ->createQueryBuilder('m')
                ->select('COUNT(m.id)')
                ->join('m.conversation', 'c')
                ->where('c.company = :company')
                ->andWhere('m.createdAt BETWEEN :start AND :end')
                ->setParameter('company', $company)
                ->setParameter('start', $day)
                ->setParameter('end', $nextDay)
                ->getQuery()
                ->getSingleScalarResult();

            $chartLabels[] = $day->format('d/m');
            $chartData[] = $count;
        }

        return $this->render('client/dashboard.html.twig', [
            'messagesCount' => $messagesCount,
            'conversationsCount' => $conversationsCount,
            'messagesLast24h' => $messagesLast24h,
            'lastConversations' => $lastConversations,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Espace Client');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToDashboard('Dashboard Client', 'fa fa-home'),
            MenuItem::linkTo(ConversationClientCrudController::class, 'Conversations', 'fa fa-comments'),
            MenuItem::linkTo(MessageClientCrudController::class, 'Messages', 'fa fa-envelope'),
        ];
    }
}