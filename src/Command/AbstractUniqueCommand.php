<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractUniqueCommand extends Command
{
    use LockableTrait;

    public function __construct()
    {
        parent::__construct();
    }

    abstract protected function perform(InputInterface $input, OutputInterface $output): int;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $command = $input->getArgument('command');

        if ($this->lock($command) === false) {
            $output->writeln(
                sprintf('The command [%s] is already running in another process.', $command)
            );

            return self::FAILURE;
        }

        try {
            return $this->perform($input, $output);
        } finally {
            $this->release();
        }
    }
}
