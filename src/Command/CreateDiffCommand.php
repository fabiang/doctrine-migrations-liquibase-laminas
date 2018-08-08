<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Toilal\Doctrine\Migrations\Liquibase\LiquibaseSchemaTool;

class CreateDiffCommand extends AbstractCommand
{

    protected function configure()
    {
        $this->setName('orm:liquibase:creatediff')
            ->setDescription('Create Liquibase diff of current changes of the entities');
    }

    protected function executeSchemaCommand(InputInterface $input, OutputInterface $output, LiquibaseSchemaTool $schemaTool, array $metadatas, SymfonyStyle $ui): int
    {
        $changelog = $schemaTool->diffChangeLog(null, $metadatas);

        $ui->text('The following changes would be applied:');
        $ui->newLine();

        $ui->text($changelog->saveXML());

        return 0;
    }
}
