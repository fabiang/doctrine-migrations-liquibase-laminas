<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\UnknownManagerException;
use Fabiang\DoctrineMigrationsLiquibase\ORM\MultiEntityManagerProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;

#[CoversClass(MultiEntityManagerProviderFactory::class)]
#[UsesClass(MultiEntityManagerProvider::class)]
final class MultiEntityManagerProviderFactoryTest extends TestCase
{
    use ProphecyTrait;

    private MultiEntityManagerProviderFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new MultiEntityManagerProviderFactory();
    }

    public function testInvoke(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->get('config')->shouldBeCalled()->willReturn([
            'doctrine' => [
                'entitymanager' => [
                    'orm_default' => [],
                    'orm_test'    => [],
                ],
            ],
        ]);

        $entityManager1 = $this->prophesize(EntityManagerInterface::class);
        $entityManager2 = $this->prophesize(EntityManagerInterface::class);

        $container->has('doctrine.entitymanager.orm_default')->shouldBeCalled()->willReturn(true);
        $container->has('doctrine.entitymanager.orm_test')->shouldBeCalled()->willReturn(true);

        $container->get('doctrine.entitymanager.orm_default')->shouldBeCalled()->willReturn($entityManager1->reveal());
        $container->get('doctrine.entitymanager.orm_test')->shouldBeCalled()->willReturn($entityManager2->reveal());

        $object = $this->factory->__invoke($container->reveal(), MultiEntityManagerProvider::class, []);

        $this->assertInstanceOf(MultiEntityManagerProvider::class, $object);
        $this->assertCount(2, $object->getEntityManagers());
    }

    public function testInvokeUnknownManager(): void
    {
        $this->expectException(UnknownManagerException::class);

        $container = $this->prophesize(ContainerInterface::class);
        $container->get('config')->shouldBeCalled()->willReturn([
            'doctrine' => [
                'entitymanager' => [
                    'orm_default' => [],
                    'orm_test'    => [],
                ],
            ],
        ]);

        $entityManager1 = $this->prophesize(EntityManagerInterface::class);

        $container->has('doctrine.entitymanager.orm_default')->shouldBeCalled()->willReturn(true);
        $container->has('doctrine.entitymanager.orm_test')->shouldBeCalled()->willReturn(false);

        $container->get('doctrine.entitymanager.orm_default')->shouldBeCalled()->willReturn($entityManager1->reveal());
        $container->get('doctrine.entitymanager.orm_test')->shouldNotBeCalled();

        $this->factory->__invoke($container->reveal(), MultiEntityManagerProvider::class, []);
    }
}
