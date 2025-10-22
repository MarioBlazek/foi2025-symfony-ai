<?php

namespace App\Command;

use App\Agent\ValidatedFOIAssistantAgent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:foi-validated-assistant',
    description: 'Multi-agent FOI assistant with answer validation (OpenAI + Anthropic)'
)]
class ValidatedFOIAssistantCommand extends Command
{
    public function __construct(
        private readonly ValidatedFOIAssistantAgent $agent
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('FOI Validated AI Assistant');
        $io->text([
            'This assistant uses a multi-agent workflow:',
            '  1. OpenAI (GPT-4o-mini) generates answers to your questions',
            '  2. Anthropic Claude (Sonnet 3.5) validates the quality of those answers',
            '',
            'This demonstrates agent orchestration and quality assurance patterns.',
        ]);
        $io->text('Type "exit" to quit.');

        $helper = $this->getHelper('question');

        while (true) {
            $question = new Question("\n📚 Your question: ");
            $userInput = $helper->ask($input, $output, $question);

            if ($userInput === null || strtolower(trim($userInput)) === 'exit') {
                $io->success('Thank you for using the Validated FOI Assistant!');
                break;
            }

            if (empty(trim($userInput))) {
                continue;
            }

            $io->newLine();
            $io->section('🤖 Processing your question...');

            try {
                $result = $this->agent->answerWithValidation($userInput);

                // Display the answer
                $io->section('💬 Answer (from OpenAI GPT-4o-mini):');
                $io->writeln($result['answer']);

                // Display the validation
                $io->newLine();
                $io->section('✓ Validation (from Anthropic Claude):');
                $io->writeln($result['validation']);

            } catch (\Exception $e) {
                $io->error('An error occurred: ' . $e->getMessage());
                $io->note('Make sure both OPENAI_API_KEY and ANTHROPIC_API_KEY are set in your .env file');
            }
        }

        return Command::SUCCESS;
    }
}
