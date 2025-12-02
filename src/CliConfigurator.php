<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase;

use Fabiang\DoctrineMigrationsLiquibase\ORM\MultiEntityManagerProvider;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;

class CliConfigurator
{
    private array $commands = [
        'doctrine.liquibase.createchangelog',
        'doctrine.liquibase.creatediff',
    ];

    public function __construct(private ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function configure(Application $cli): void
    {
        foreach ($this->commands as $commandName) {
            /** @var Command $command */
            $command = $this->container->get($commandName);
            $command->getDefinition()->addOption($this->createObjectManagerInputOption());
            $cli->add($command);
        }
    }

    private function createObjectManagerInputOption(): InputOption
    {
        return new InputOption(
            'em',
            null,
            InputOption::VALUE_OPTIONAL,
            'The name of the entity manager to use.',
            MultiEntityManagerProvider::DEFAULT_ENTITYMANAGER_NAME
        );
    }
}
