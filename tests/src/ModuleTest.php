<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase;

use Fabiang\DoctrineMigrationsLiquibase\CliConfigurator;
use Fabiang\DoctrineMigrationsLiquibase\ConfigProvider;
use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\ModuleManager\ModuleManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

use function call_user_func;
use function is_callable;

#[CoversClass(Module::class)]
#[UsesClass(ConfigProvider::class)]
final class ModuleTest extends TestCase
{
    use ProphecyTrait;

    private Module $module;

    protected function setUp(): void
    {
        $this->module = new Module();
    }

    public function testInit(): void
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

    public function testGetServiceConfig(): void
    {
        $this->assertIsArray($this->module->getServiceConfig());
    }

    public function testGetModuleDependencies(): void
    {
        $this->assertSame(['DoctrineModule'], $this->module->getModuleDependencies());
    }
}
