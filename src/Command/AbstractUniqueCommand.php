<?php

declare(strict_types=1);

namespace App\Command;

use App\Component\Validator\Exception\AcceptanceFailedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractUniqueCommand extends Command
{
    use LockableTrait;

    public function __construct(private readonly ValidatorInterface $validator)
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

    protected function validateRawValue(array $collection): void
    {
        $input = [];
        $constraint = [];

        foreach ($collection as $item) {
            $input[$item[0]] = $item[1];
            $constraint[$item[0]] = $item[2];
        }

        $violations = $this->validator->validate(
            $input,
            new Assert\Collection($constraint)
        );

        if (count($violations) > 0) {
            throw new AcceptanceFailedException(null, $violations);
        }
    }
}
