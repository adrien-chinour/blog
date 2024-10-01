<?php

declare(strict_types=1);

namespace App\Presentation\Cli;

use App\Application\Command\SearchArticleInitIndexation\SearchArticleInitIndexationCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand('search:articles:index')]
final class IndexArticlesCommand extends Command
{
    public function __construct(private MessageBusInterface $messageBus)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->messageBus->dispatch(new SearchArticleInitIndexationCommand());

        return Command::SUCCESS;
    }
}
