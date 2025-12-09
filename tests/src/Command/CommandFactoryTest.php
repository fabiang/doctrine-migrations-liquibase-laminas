<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase\Command;

use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Fabiang\DoctrineMigrationsLiquibase\Command\AbstractCommand;
use Fabiang\DoctrineMigrationsLiquibase\ORM\MultiEntityManagerProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;

#[CoversClass(CommandFactory::class)]
#[UsesClass(AbstractCommand::class)]
#[UsesClass(CreateChangelogCommand::class)]
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
        $container->has('config')->willReturn(true);
        $container->get('config')->shouldBeCalled()->willReturn([
            'doctrine' => [
                'liquibase' => [
                    'ignore_tables' => 'test_table',
                ],
            ],
        ]);

        $instance = $this->factory->__invoke($container->reveal(), CreateChangelogCommand::class, []);
        $this->assertInstanceOf(CreateChangelogCommand::class, $instance);
        $this->assertSame(['test_table'], $instance->options->getIgnoreTables());
    }
}
