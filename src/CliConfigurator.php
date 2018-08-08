<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase;

use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputOption;

class CliConfigurator
{
    private $defaultObjectManagerName = 'doctrine.entitymanager.orm_default';
    private $commands                 = [
        'doctrine.liquibase.createchangelog',
        'doctrine.liquibase.creatediff',
    ];

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function configure(Application $cli): void
    {
        $commands = $this->getAvailableCommands();
        foreach ($commands as $commandName) {
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

    private function getObjectManagerName(): string
    {
        $arguments = new ArgvInput();
        if (!$arguments->hasParameterOption('--object-manager')) {
            return $this->defaultObjectManagerName;
        }
        return $arguments->getParameterOption('--object-manager');
    }

    private function getAvailableCommands(): array
    {
        return $this->commands;
    }
}
