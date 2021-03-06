<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Fabiang\Doctrine\Migrations\Liquibase\LiquibaseSchemaTool;

abstract class AbstractCommand extends Command
{

    /**
     * @param ClassMetadata[] $metadatas
     *
     * @return int 0 if everything went fine, or an error code.
     */
    abstract protected function executeSchemaCommand(InputInterface $input, OutputInterface $output, LiquibaseSchemaTool $schemaTool, array $metadatas, SymfonyStyle $ui): int;

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $ui        = new SymfonyStyle($input, $output);
        $emHelper  = $this->getHelper('em');
        /** @var EntityManagerInterface $em */
        $em        = $emHelper->getEntityManager();
        $metadatas = $em->getMetadataFactory()->getAllMetadata();

        if (empty($metadatas)) {
            $ui->success('No Metadata Classes to process.');
            return 0;
        }

        return $this->executeSchemaCommand($input, $output, new LiquibaseSchemaTool($em), $metadatas, $ui);
    }
}
