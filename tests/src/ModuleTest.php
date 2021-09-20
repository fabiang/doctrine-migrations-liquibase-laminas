<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Laminas\ModuleManager\ModuleManagerInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\EventManager\EventInterface;
use Interop\Container\ContainerInterface;
use Fabiang\DoctrineMigrationsLiquibase\CliConfigurator;
use Symfony\Component\Console\Application;

/**
 * @coversDefaultClass Fabiang\DoctrineMigrationsLiquibase\Module
 */
final class ModuleTest extends TestCase
{

    use ProphecyTrait;

    private Module $module;

    protected function setUp(): void
    {
        $this->module = new Module();
    }

    /**
     * @test
     * @covers ::init
     */
    public function init(): void
    {
        $sharedEventManager = $this->prophesize(SharedEventManagerInterface::class);
        $sharedEventManager->attach(
                'doctrine',
                'loadCli.post',
                Argument::that(function ($arg) {
                    $app = $this->prophesize(Application::class)->reveal();

                    $e = $this->prophesize(EventInterface::class);
                    $e->getTarget()->willReturn($app);

                    $cliConfigurator = $this->prophesize(CliConfigurator::class);
                    $cliConfigurator->configure($app)->shouldBeCalled();

                    $sm = $this->prophesize(ContainerInterface::class);
                    $sm->get(CliConfigurator::class)->willReturn($cliConfigurator->reveal());

                    $e->getParam('ServiceManager')->willReturn($sm->reveal());

                    call_user_func($arg, $e->reveal());
                    return is_callable($arg);
                }),
                1
            )
            ->shouldBeCalled();

        $eventManager = $this->prophesize(EventManagerInterface::class);

        $eventManager->getSharedManager()->willReturn($sharedEventManager->reveal());

        $moduleManager = $this->prophesize(ModuleManagerInterface::class);
        $moduleManager->getEventManager()->willReturn($eventManager->reveal());

        $this->module->init($moduleManager->reveal());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::getServiceConfig
     */
    public function getServiceConfig(): void
    {
        $this->assertIsArray($this->module->getServiceConfig());
    }

    /**
     * @test
     * @covers ::getModuleDependencies
     */
    public function getModuleDependencies(): void
    {
        $this->assertSame(['DoctrineModule'], $this->module->getModuleDependencies());
    }

}
