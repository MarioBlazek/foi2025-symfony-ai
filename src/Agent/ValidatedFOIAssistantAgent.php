<?php

declare(strict_types=1);

namespace App\Agent;

/**
 * Multi-agent orchestrator that demonstrates a validation workflow:
 * 1. OpenAI generates an answer to a student question
 * 2. Anthropic Claude validates the quality of that answer
 *
 * This pattern is useful for:
 * - Quality assurance in production systems
 * - Demonstrating multi-agent collaboration
 * - Comparing outputs from different LLM providers
 * - Teaching students about agent orchestration patterns
 */
class ValidatedFOIAssistantAgent
{
    public function __construct(
        private readonly FOIStudentQuestionAgent $questionAgent,
        private readonly AnswerValidatorAgent $validatorAgent,
    ) {}

    /**
     * Processes a question through a two-stage validation workflow.
     *
     * @return array{question: string, answer: string, validation: string}
     */
    public function answerWithValidation(string $question): array
    {
        // Stage 1: Generate answer using OpenAI
        $answer = $this->questionAgent->answerQuestion($question);

        // Stage 2: Validate answer using Anthropic Claude
        $validation = $this->validatorAgent->validate($question, $answer);

        return [
            'question' => $question,
            'answer' => $answer,
            'validation' => $validation,
        ];
    }

    /**
     * Simplified method that returns just the answer.
     * Useful when you only need the final output without validation details.
     */
    public function answerQuestion(string $question): string
    {
        return $this->questionAgent->answerQuestion($question);
    }
}
