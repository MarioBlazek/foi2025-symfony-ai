<?php

namespace App\Command;

use App\Agent\WikipediaSearchAgent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:wikipedia-search', description: 'AI-powered Wikipedia search assistant')]
class WikipediaSearchCommand extends Command
{
    public function __construct(
        private readonly WikipediaSearchAgent $agent
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Wikipedia Search Assistant');
        $io->text('Ask me anything and I\'ll search Wikipedia for you!');
        $io->text('Type "exit" to quit.');

        $helper = $this->getHelper('question');

        while (true) {
            $question = new Question("\n🔍 Your search query: ");
            $userInput = $helper->ask($input, $output, $question);

            if (strtolower($userInput) === 'exit') {
                $io->success('Thank you for using Wikipedia Search Assistant!');
                break;
            }

            $io->section('Wikipedia Search Results:');
            $response = $this->agent->search($userInput);
            $io->writeln($response);
        }

        return Command::SUCCESS;
    }
}
