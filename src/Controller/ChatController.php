<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CompanyRepository;
use App\Service\ChatService;


final class ChatController extends AbstractController
{
    #[Route('/api/chat', name: 'api_chat', methods: ['POST'])]
    public function chat(
        Request $request,
        CompanyRepository $companyRepository,
        ChatService $chatService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['companyId'], $data['sessionId'], $data['message'])) {
            return new JsonResponse(['error' => 'Requete invalide. Parametres manquants'], 400);
        }

        $company = $companyRepository->find($data['companyId']);
        if (!$company) {
            return new JsonResponse(['error' => 'Entreprise non trouvee'], 404);
        }

        $response = $chatService->handleMessage($company, $data['sessionId'], $data['message']);
        return new JsonResponse(['response' => $response]);
    }
}
