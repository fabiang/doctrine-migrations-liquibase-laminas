<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class CliConfiguratorFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): CliConfigurator
    {
        return new CliConfigurator($container);
    }
}
