<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase\Command;

use Fabiang\DoctrineMigrationsLiquibase\ORM\MultiEntityManagerProvider;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Override;
use Psr\Container\ContainerInterface;

final class CommandFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     */
    #[Override]
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): AbstractCommand
    {
        $ignoreTables = [];
        if ($container->has('config')) {
            $ignoreTables = $container->get('config')['doctrine']['liquibase']['ignore_tables'] ?? [];
        }

        return new $requestedName($container->get(MultiEntityManagerProvider::class), (array) $ignoreTables);
    }
}
