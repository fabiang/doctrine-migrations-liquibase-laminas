<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase;

use Laminas\ModuleManager\Feature\InitProviderInterface;
use Laminas\ModuleManager\Feature\ServiceProviderInterface;
use Laminas\ModuleManager\ModuleManagerInterface;
use Fabiang\DoctrineMigrationsLiquibase\CliConfigurator;
use Laminas\ModuleManager\Feature\DependencyIndicatorInterface;
use Laminas\EventManager\EventInterface;

final class Module implements InitProviderInterface, ServiceProviderInterface, DependencyIndicatorInterface
{

    private ConfigProvider $configProvider;

    public function __construct()
    {
        $this->configProvider = new ConfigProvider();
    }

    public function init(ModuleManagerInterface $manager): void
    {
        // Initialize the console
        $manager->getEventManager()
            ->getSharedManager()
            ->attach(
                'doctrine',
                'loadCli.post',
                function (EventInterface $event) {
                    $event->getParam('ServiceManager')
                    ->get(CliConfigurator::class)
                    ->configure($event->getTarget());
                },
                1
        );
    }

    public function getServiceConfig(): array
    {
        return $this->configProvider->getDependencies();
    }

    public function getModuleDependencies(): array
    {
        return ['DoctrineModule'];
    }

}
