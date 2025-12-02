<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\UnknownManagerException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(MultiEntityManagerProvider::class)]
final class MultiEntityManagerProviderTest extends TestCase
{
    use ProphecyTrait;

    private MultiEntityManagerProvider $provider;
    private EntityManagerInterface $entityProvider1;
    private EntityManagerInterface $entityProvider2;

    protected function setUp(): void
    {
        $this->entityProvider1 = $this->prophesize(EntityManagerInterface::class)->reveal();
        $this->entityProvider2 = $this->prophesize(EntityManagerInterface::class)->reveal();

        $this->provider = new MultiEntityManagerProvider([
            MultiEntityManagerProvider::DEFAULT_ENTITYMANAGER_NAME                => $this->entityProvider1,
            MultiEntityManagerProvider::DEFAULT_ENTITYMANAGER_PREFIX . 'orm_test' => $this->entityProvider2,
        ]);
    }

    public function testGetEntityManagers(): void
    {
        $this->assertSame(
            [
                MultiEntityManagerProvider::DEFAULT_ENTITYMANAGER_NAME                => $this->entityProvider1,
                MultiEntityManagerProvider::DEFAULT_ENTITYMANAGER_PREFIX . 'orm_test' => $this->entityProvider2,
            ],
            $this->provider->getEntityManagers()
        );
    }

    public function testGetDefaultManager(): void
    {
        $this->assertSame($this->entityProvider1, $this->provider->getDefaultManager());
    }

    public function testGetManager(): void
    {
        $this->assertSame(
            $this->entityProvider1,
            $this->provider->getManager(MultiEntityManagerProvider::DEFAULT_ENTITYMANAGER_NAME)
        );

        $this->assertSame(
            $this->entityProvider2,
            $this->provider->getManager(MultiEntityManagerProvider::DEFAULT_ENTITYMANAGER_PREFIX . 'orm_test')
        );
    }

    public function testGetManagerUnknown(): void
    {
        $this->expectException(UnknownManagerException::class);
        $this->provider->getManager('foo');
    }
}
