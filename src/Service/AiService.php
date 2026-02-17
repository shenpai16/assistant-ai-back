<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class AiService
{
    private string $apiUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $apiKey
    ) {}

    public function generate(string $context, string $message): string{
        $systemPrompt = <<<EOT
        Tu es l'assistant commercial d'une entreprise locale.
        Ton rôle :
        - répondre clairement, professionnellement et rapidement
        - utiliser uniquement les informations fournies dans le contexte
        - ne jamais inventer de prix ou de services non mentionnés
        - proposer de contacter l'entreprise si une information manque
        EOT;

        $payload = [
            'model' => 'gpt-4.1-mini',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'assistant', 'content' => $context],
                ['role' => 'user', 'content' => $message],
            ],
            'temperature' => 0.4,
            'max_tokens' => 300,
        ];

        try {
            $response = $this->httpClient->request('POST', $this->apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $data = $response->toArray();

            return $data['choices'][0]['message']['content'] ?? 'Désolé, je n\'ai pas pu générer de réponse.';
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            return 'Désolé, une erreur est survenue lors de la génération de la réponse.';
        }
    }
}