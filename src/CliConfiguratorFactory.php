<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase;

use Zend\ServiceManager\FactoryInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

final class CliConfiguratorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CliConfigurator
    {
        return new CliConfigurator($container);
    }

    public function createService(ServiceLocatorInterface $serviceLocator): CliConfigurator
    {
        return $this($serviceLocator, CliConfigurator::class, []);
    }
}
