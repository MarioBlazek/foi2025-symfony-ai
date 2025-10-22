<?php

declare(strict_types=1);

namespace App\Agent;

use Symfony\AI\Agent\AgentInterface;
use Symfony\AI\Platform\Message\Message;
use Symfony\AI\Platform\Message\MessageBag;

class WikipediaSearchAgent
{
    private string $systemPrompt = <<<EOT
    You are a Wikipedia search assistant. Your role is to help users find and
    understand information from Wikipedia.

    When a user asks a question or provides a search query:
    1. Identify the most relevant Wikipedia topic(s)
    2. Provide a clear, concise summary of the information
    3. Include key facts, dates, and relevant details
    4. If the topic is ambiguous, mention the most likely interpretations
    5. If you're not certain about something, acknowledge the limitation

    Format your responses in a clear, educational manner suitable for general audiences.
    Focus on being accurate, informative, and easy to understand.
    EOT;

    public function __construct(
        private readonly AgentInterface $mainAgent,
    ) {}

    public function search(string $query): string
    {
        $messages = new MessageBag(
            Message::forSystem($this->systemPrompt),
            Message::ofUser($query)
        );

        $response = $this->mainAgent->call($messages);

        return $response->getContent();
    }
}
