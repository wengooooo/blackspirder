<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022 Kai Sassnowski
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/wengooooo/blackspirder
 */

namespace BlackSpider\Shell\Commands;

use BlackSpider\BlackSpider;
use BlackSpider\Shell\InvalidSpiderException;
use BlackSpider\Shell\Resolver\NamespaceResolverInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'roach:run')]
final class RunSpiderCommand extends Command
{
    protected static $defaultName = 'roach:run';

    protected static $defaultDescription = 'Start a spider run for the provided spider class';

    protected function configure(): void
    {
        $this->addArgument('spider', InputArgument::REQUIRED, 'The spider class to execute');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $resolver = BlackSpider::resolve(NamespaceResolverInterface::class);

        try {
            /** @psalm-suppress MixedArgument */
            $spiderClass = $resolver->resolveSpiderNamespace($input->getArgument('spider'));
        } catch (InvalidSpiderException $exception) {
            $output->writeln(\sprintf('<error>Invalid spider: %s</error>', $exception->getMessage()));

            return self::FAILURE;
        }

        BlackSpider::startSpider($spiderClass);

        return self::SUCCESS;
    }
}
