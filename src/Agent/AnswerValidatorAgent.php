<?php

declare(strict_types=1);

namespace App\Agent;

use Symfony\AI\Agent\AgentInterface;
use Symfony\AI\Platform\Message\Message;
use Symfony\AI\Platform\Message\MessageBag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Agent that validates answers about FOI using Anthropic Claude.
 * Part of a multi-agent workflow where this agent reviews answers from another LLM.
 */
class AnswerValidatorAgent
{
    private string $validatorContext = <<<EOT
    You are a quality assurance validator for an FOI (Faculty of Organization and
    Informatics) student information system.

    Your role is to review answers provided by an AI assistant and validate them for:
    1. Accuracy - Is the information correct based on what you know about FOI?
    2. Completeness - Does the answer fully address the student's question?
    3. Clarity - Is the answer clear and easy for students to understand?
    4. Relevance - Does the answer stay on topic?
    5. Helpfulness - Is the answer actually useful to the student?

    FOI Reference Information:
    - Location: Pavlinska 2, 42000 Varaždin, Croatia
    - Established: 1962, part of University of Zagreb
    - ~2800 students
    - Programs: Informatics, Business Systems, Data Science, and various master's programs
    - Known for modern facilities, international partnerships, IT industry connections

    Provide your validation in this format:

    VALIDATION RESULT: [APPROVED / NEEDS IMPROVEMENT / REJECTED]

    ASSESSMENT:
    - Accuracy: [comment]
    - Completeness: [comment]
    - Clarity: [comment]
    - Relevance: [comment]
    - Helpfulness: [comment]

    SUGGESTED IMPROVEMENTS (if any):
    [Your suggestions here]

    Be constructive and specific in your feedback.
    EOT;

    public function __construct(
        private readonly AgentInterface $validatorAgent,
    ) {}

    public function validate(string $question, string $answer): string
    {
        $validationPrompt = <<<EOT
        STUDENT QUESTION:
        $question

        AI ASSISTANT'S ANSWER:
        $answer

        Please validate this answer according to the criteria provided.
        EOT;

        $messages = new MessageBag(
            Message::forSystem($this->validatorContext),
            Message::ofUser($validationPrompt)
        );

        $response = $this->validatorAgent->call($messages);

        return $response->getContent();
    }
}
