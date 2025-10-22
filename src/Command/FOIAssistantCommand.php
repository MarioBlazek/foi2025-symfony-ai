<?php

namespace App\Command;

use App\Agent\FOIAssistantAgent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:foi-assistant', description: 'AI assistant that answers questions about FOI')]
class FOIAssistantCommand extends Command
{
    public function __construct(
        private readonly FOIAssistantAgent $agent
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('FOI AI Assistant');
        $io->text('Ask me anything about the Faculty of Organization and Informatics!');
        $io->text('Type "exit" to quit.');

        $helper = $this->getHelper('question');

        while (true) {
            $question = new Question("\n📚 Your question: ");
            $userInput = $helper->ask($input, $output, $question);

            if (strtolower($userInput) === 'exit') {
                $io->success('Thank you for using FOI Assistant!');
                break;
            }

            $io->section('FOI Assistant Response:');
            $response = $this->agent->answerQuestion($userInput);
            $io->writeln($response);
        }

        return Command::SUCCESS;
    }
}
