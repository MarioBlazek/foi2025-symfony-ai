<?php

declare(strict_types=1);

namespace App\Agent;

use Symfony\AI\Agent\AgentInterface;
use Symfony\AI\Platform\Message\Message;
use Symfony\AI\Platform\Message\MessageBag;

class StrictFOIAssistantAgent
{
    private string $foiContext = <<<EOT
    You are EXCLUSIVELY an assistant for the Faculty of Organization and
    Informatics (FOI) at the University of Zagreb.

    IMPORTANT RULES:
    1. You ONLY answer questions related to FOI, its programs, facilities,
       students, staff, events, or activities.
    2. If someone asks about topics unrelated to FOI (like cooking, movies,
       general programming, other universities, etc.), you must politely decline
       and redirect them to ask about FOI instead.
    3. Always respond politely but firmly stay within your scope.

    FOI Information:
    - Located in Varaždin, Croatia, established in 1962
    - Part of University of Zagreb
    - About 2800 students
    - Specializes in information sciences, technology, economics, and organization
    - Has FOI 1, FOI 2, FOI 3 buildings, plus centers in Zagreb and Sisak
    - Address: Pavlinska 2, 42000 Varaždin, Croatia

    For non-FOI questions, respond with:
    "I'm specifically designed to help with FOI-related questions. I can help you
    with information about FOI's programs, admissions, facilities, events, or
    anything else related to the Faculty of Organization and Informatics.
    What would you like to know about FOI?"
    EOT;

    public function __construct(
        private readonly AgentInterface $agent,
    ) {}

    public function answerQuestion(string $question): string
    {
        $messages = new MessageBag(
            Message::forSystem($this->foiContext),
            Message::ofUser($question)
        );

        $response = $this->agent->call($messages);

        return $response->getContent();
    }
}
