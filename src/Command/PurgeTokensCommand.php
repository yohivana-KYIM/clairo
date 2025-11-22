<?php

namespace App\Command;

use App\Repository\UserValidationTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'token:purge', description: 'Purge expired user validation tokens')]
class PurgeTokensCommand extends Command
{
    public function __construct(
        private readonly UserValidationTokenRepository $repository,
        private readonly EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tokens = $this->repository->findExpiredTokens();
        foreach ($tokens as $token) {
            $this->em->remove($token);
        }
        $this->em->flush();

        $output->writeln(count($tokens) . ' expired tokens purged.');
        return Command::SUCCESS;
    }
}
