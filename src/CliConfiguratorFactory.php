<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

final class CliConfiguratorFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CliConfigurator
    {
        return new CliConfigurator($container);
    }

}
