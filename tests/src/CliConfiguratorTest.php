<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

#[CoversClass(CliConfigurator::class)]
final class CliConfiguratorTest extends TestCase
{
    use ProphecyTrait;

    private CliConfigurator $object;
    private ObjectProphecy $container;

    protected function setUp(): void
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->object    = new CliConfigurator($this->container->reveal());
    }

    public function testConfigure(): void
    {
        $app = $this->prophesize(Application::class);

        $inputOption = $this->prophesize(InputDefinition::class);
        $inputOption->addOption(Argument::type(InputOption::class))
            ->shouldBeCalledTimes(2);

        $cmd1 = $this->prophesize(Command::class);
        $cmd1->getDefinition()->willReturn($inputOption->reveal());
        $cmd2 = $this->prophesize(Command::class);
        $cmd2->getDefinition()->willReturn($inputOption->reveal());

        $this->container->get('doctrine.liquibase.createchangelog')
            ->shouldBeCalled()
            ->willReturn($cmd1->reveal());

        $this->container->get('doctrine.liquibase.creatediff')
            ->shouldBeCalled()
            ->willReturn($cmd1->reveal());

        $app->add(Argument::type(Command::class))->shouldBeCalledTimes(2);

        $this->object->configure($app->reveal());
    }
}
