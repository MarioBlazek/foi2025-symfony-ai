<?php

declare(strict_types=1);

namespace App\Agent;

use Symfony\AI\Agent\AgentInterface;
use Symfony\AI\Platform\Message\Message;
use Symfony\AI\Platform\Message\MessageBag;

class FOIAssistantAgent
{
    private string $foiContext = <<<EOT
    You are a knowledgeable assistant for the Faculty of Organization and
    Informatics (FOI) at the University of Zagreb. FOI is located in Varaždin,
    Croatia, and was established in 1962. It specializes in information sciences,
    technology, economics, and organization. The faculty has about 2800 students
    and offers programs in informatics, business systems, data science, and
    entrepreneurship. FOI is known for its modern facilities, international
    partnerships, and strong connection with the IT industry. The campus includes
    FOI 1, FOI 2, and FOI 3 buildings, with centers in Zagreb and Sisak as well.
    Address: Pavlinska 2, 42000 Varaždin, Croatia.
    EOT;

    public function __construct(
        private readonly AgentInterface $mainAgent,
    ) {}

    public function answerQuestion(string $question): string
    {
        $messages = new MessageBag(
            Message::forSystem($this->foiContext),
            Message::ofUser($question)
        );

        $response = $this->mainAgent->call($messages);

        return $response->getContent();
    }
}
