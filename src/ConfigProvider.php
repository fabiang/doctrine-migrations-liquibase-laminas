<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase;

use Zend\ServiceManager\Factory\InvokableFactory;

class ConfigProvider
{

    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'aliases'   => [
                'doctrine.liquibase.createchangelog' => Command\CreateChangelogCommand::class,
                'doctrine.liquibase.creatediff'      => Command\CreateDiffCommand::class,
            ],
            'factories' => [
                CliConfigurator::class                => CliConfiguratorFactory::class,
                Command\CreateChangelogCommand::class => InvokableFactory::class,
                Command\CreateDiffCommand::class      => InvokableFactory::class,
            ],
        ];
    }
}
