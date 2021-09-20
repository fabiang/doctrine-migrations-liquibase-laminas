<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Interop\Container\ContainerInterface;

/**
 * @coversDefaultClass Fabiang\DoctrineMigrationsLiquibase\CliConfiguratorFactory
 */
final class CliConfiguratorFactoryTest extends TestCase
{

    use ProphecyTrait;

    private CliConfiguratorFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new CliConfiguratorFactory();
    }

    /**
     * @test
     * @covers ::__invoke
     */
    public function invoke(): void
    {
        $this->assertIsCallable($this->factory);
        $container = $this->prophesize(ContainerInterface::class);

        $this->assertInstanceOf(
            CliConfigurator::class,
            $this->factory->__invoke($container->reveal(), CliConfigurator::class, [])
        );
    }

}
