<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase\ORM;

use Doctrine\ORM\Tools\Console\EntityManagerProvider\UnknownManagerException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Override;
use Psr\Container\ContainerInterface;

use function array_keys;
use function assert;
use function count;

final class MultiEntityManagerProviderFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     */
    #[Override]
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): MultiEntityManagerProvider {
        $entityManagers = [];

        $entityManagerNames = array_keys($container->get('config')['doctrine']['entitymanager'] ?? []);
        foreach ($entityManagerNames as $entityManagerName) {
            $entityManagerName = MultiEntityManagerProvider::DEFAULT_ENTITYMANAGER_PREFIX . $entityManagerName;

            if (! $container->has($entityManagerName)) {
                throw UnknownManagerException::unknownManager($entityManagerName, array_keys($entityManagers));
            }

            $entityManagers[$entityManagerName] = $container->get($entityManagerName);
        }

        assert(count($entityManagers) > 0, 'There are no Doctrine EntityManagers defined');

        return new MultiEntityManagerProvider($entityManagers);
    }
}
