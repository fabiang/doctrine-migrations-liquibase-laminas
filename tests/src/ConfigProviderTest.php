<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConfigProvider::class)]
final class ConfigProviderTest extends TestCase
{
    private ConfigProvider $object;

    protected function setUp(): void
    {
        $this->object = new ConfigProvider();
    }

    public function testInvoke()
    {
        $this->assertIsCallable($this->object);
        $this->assertIsArray($this->object->__invoke());
    }

    public function testGetDependencies()
    {
        $this->assertIsArray($this->object->getDependencies());
        $this->assertArrayHasKey('aliases', $this->object->getDependencies());
        $this->assertArrayHasKey('factories', $this->object->getDependencies());
    }
}
