<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase\Command;

use Fabiang\Doctrine\Migrations\Liquibase\LiquibaseSchemaTool;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateChangelogCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('orm:liquibase:createchangelog')
            ->setDescription('Create full Liquibase changelog of the current state of the entities');
    }

    protected function executeSchemaCommand(
        InputInterface $input,
        OutputInterface $output,
        LiquibaseSchemaTool $schemaTool,
        array $metadatas,
        SymfonyStyle $ui
    ): int {
        $changelog = $schemaTool->changeLog(null, $metadatas);

        $ui->text('Full changelog file of the current entity state:');
        $ui->newLine();

        $ui->text($changelog->saveXML());

        return 0;
    }
}
