<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\Conversation;
use App\Entity\Message;
use App\Repository\ConversationRepository;

class ChatService
{
    public function __construct(
        private readonly ConversationRepository $conversationRepository,
        private readonly AiService $aiService,
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function handleMessage(Company $company, string $sessionId, string $userMessage): string
    {
        $conversation = $this->conversationRepository->findOneBy(['company' => $company, 'sessionId' => $sessionId]);

        if (!$conversation) {
            $conversation = new Conversation();
            $conversation->setCompany($company);
            $conversation->setSessionId($sessionId);
            $conversation->setCreatedAt(new \DateTimeImmutable());
            $this->entityManager->persist($conversation);
        }

        $msgUser = new Message();
        $msgUser->setConversation($conversation);
        $msgUser->setRole('user');
        $msgUser->setContent($userMessage);
        $msgUser->setCreatedAt(new \DateTimeImmutable());
        $this->entityManager->persist($msgUser);

        $context = $company->getContext(); 
        $aiResponse = $this->aiService->generate($context, $userMessage);

        $msgAi = new Message();
        $msgAi->setConversation($conversation);
        $msgAi->setRole('assistant');
        $msgAi->setContent($aiResponse);
        $msgAi->setCreatedAt(new \DateTimeImmutable());
        $this->entityManager->persist($msgAi);

        $this->entityManager->flush();

        return $aiResponse;
    }
}