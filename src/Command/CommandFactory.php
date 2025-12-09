<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase\Command;

use Fabiang\Doctrine\Migrations\Liquibase\Options;
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
        $liquibaseOptions = new Options();

        if ($container->has('config')) {
            $ignoreTables = $container->get('config')['doctrine']['liquibase']['ignore_tables'] ?? [];
            $liquibaseOptions->setIgnoreTables((array) $ignoreTables);
        }

        return new $requestedName($container->get(MultiEntityManagerProvider::class), $liquibaseOptions);
    }
}
