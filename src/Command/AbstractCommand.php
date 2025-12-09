<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase\Command;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\Console\Command\AbstractEntityManagerCommand;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Fabiang\Doctrine\Migrations\Liquibase\Options;
use Fabiang\Doctrine\Migrations\Liquibase\SchemaTool;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractCommand extends AbstractEntityManagerCommand
{
    public function __construct(EntityManagerProvider $entityManagerProvider, public readonly Options $options)
    {
        parent::__construct($entityManagerProvider);
    }

    /**
     * @param ClassMetadata[] $metadatas
     * @return int 0 if everything went fine, or an error code.
     */
    abstract protected function executeSchemaCommand(
        InputInterface $input,
        OutputInterface $output,
        SchemaTool $schemaTool,
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

        return $this->executeSchemaCommand(
            $input,
            $output,
            new SchemaTool($em, $this->options),
            $metadatas,
            $ui
        );
    }
}
