<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase;

use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\ModuleManagerInterface;
use Fabiang\DoctrineMigrationsLiquibase\CliConfigurator;
use Zend\EventManager\EventInterface;

final class Module implements InitProviderInterface, ServiceProviderInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct()
    {
        $this->configProvider = new ConfigProvider();
    }

    public function init(ModuleManagerInterface $manager): void
    {
        // Initialize the console
        $manager
            ->getEventManager()
            ->getSharedManager()
            ->attach(
                'doctrine',
                'loadCli.post',
                function (EventInterface $event) {
                    $event
                    ->getParam('ServiceManager')
                    ->get(CliConfigurator::class)
                    ->configure($event->getTarget())
                    ;
                },
                1
            );
    }

    public function getServiceConfig(): array
    {
        return $this->configProvider->getDependencies();
    }
}
