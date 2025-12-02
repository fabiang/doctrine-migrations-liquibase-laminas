<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase\Command;

use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Fabiang\DoctrineMigrationsLiquibase\ORM\MultiEntityManagerProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;

#[CoversClass(CommandFactory::class)]
final class CommandFactoryTest extends TestCase
{
    use ProphecyTrait;

    private CommandFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new CommandFactory();
    }

    public function testInvoke(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->get(MultiEntityManagerProvider::class)
            ->shouldBeCalled()
            ->willReturn($this->prophesize(EntityManagerProvider::class)->reveal());

        $this->assertInstanceOf(
            CreateChangelogCommand::class,
            $this->factory->__invoke($container->reveal(), CreateChangelogCommand::class, [])
        );
    }
}
