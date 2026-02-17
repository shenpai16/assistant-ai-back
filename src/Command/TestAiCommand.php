<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'test:ai',
    description: 'Add a short description for your command',
)]
class TestAiCommand extends Command
{
    public function __construct(private \App\Service\AiService $aiService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $context = "Nous sommes un garage automobile situé à Paris. 
                    Nous faisons : réparations, vidanges, pneus, diagnostics.
                    Horaires : 9h-18h du lundi au samedi.";

        $question = "Bonjour, vous faites les vidanges pour BMW ?";

        $response = $this->aiService->generate($context, $question);
        $io->writeln("AI Response: " . $response);

        return Command::SUCCESS;
    }
}
