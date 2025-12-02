<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase\Command;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\Console\Command\AbstractEntityManagerCommand;
use Fabiang\Doctrine\Migrations\Liquibase\LiquibaseSchemaTool;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractCommand extends AbstractEntityManagerCommand
{
    /**
     * @param ClassMetadata[] $metadatas
     * @return int 0 if everything went fine, or an error code.
     */
    abstract protected function executeSchemaCommand(
        InputInterface $input,
        OutputInterface $output,
        LiquibaseSchemaTool $schemaTool,
        array $metadatas,
        SymfonyStyle $ui
    ): int;

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $ui        = new SymfonyStyle($input, $output);
        $em        = $this->getEntityManager($input);
        $metadatas = $em->getMetadataFactory()->getAllMetadata();

        if (empty($metadatas)) {
            $ui->success('No Metadata Classes to process.');
            return 0;
        }

        return $this->executeSchemaCommand($input, $output, new LiquibaseSchemaTool($em), $metadatas, $ui);
    }
}
