<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase;

use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;

class CliConfigurator
{

    private string $defaultObjectManagerName = 'doctrine.entitymanager.orm_default';
    private array $commands                 = [
        'doctrine.liquibase.createchangelog',
        'doctrine.liquibase.creatediff',
    ];
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function configure(Application $cli): void
    {
        foreach ($this->commands as $commandName) {
            /* @var $command \Symfony\Component\Console\Command\Command */
            $command = $this->container->get($commandName);
            $command->getDefinition()->addOption($this->createObjectManagerInputOption());
            $cli->add($command);
        }
    }

    private function createObjectManagerInputOption(): InputOption
    {
        return new InputOption(
            'object-manager',
            null,
            InputOption::VALUE_OPTIONAL,
            'The name of the object manager to use.',
            $this->defaultObjectManagerName
        );
    }

}
