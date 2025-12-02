<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\UnknownManagerException;
use Override;

use function array_keys;

final class MultiEntityManagerProvider implements EntityManagerProvider
{
    public const DEFAULT_ENTITYMANAGER_PREFIX = 'doctrine.entitymanager.';
    public const DEFAULT_ENTITYMANAGER_NAME   = self::DEFAULT_ENTITYMANAGER_PREFIX . 'orm_default';

    /**
     * @param list<string, EntityManagerInterface> $entityManagers
     */
    public function __construct(private array $entityManagers)
    {
    }

    #[Override]
    public function getDefaultManager(): EntityManagerInterface
    {
        return $this->getManager(static::DEFAULT_ENTITYMANAGER_NAME);
    }

    #[Override]
    public function getManager(string $name): EntityManagerInterface
    {
        if (! isset($this->entityManagers[$name])) {
            throw UnknownManagerException::unknownManager($name, array_keys($this->entityManagers));
        }

        return $this->entityManagers[$name];
    }

    /**
     * @return list<string, EntityManagerInterface>
     */
    public function getEntityManagers(): array
    {
        return $this->entityManagers;
    }
}
