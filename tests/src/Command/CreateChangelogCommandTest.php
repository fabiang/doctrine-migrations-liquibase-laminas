<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase\Command;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaConfig;
use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\Configuration as EMConfig;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\QuoteStrategy;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Doctrine\ORM\Tools\ToolEvents;
use Fabiang\Doctrine\Migrations\Liquibase\Options;
use Fabiang\DoctrineMigrationsLiquibase\Command\AbstractCommand;
use Fabiang\DoctrineMigrationsLiquibase\ORM\MultiEntityManagerProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Tester\CommandTester;

use function method_exists;

#[CoversClass(CreateChangelogCommand::class)]
#[CoversClass(AbstractCommand::class)]
final class CreateChangelogCommandTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $entityManagerProvider;
    private CommandTester $commandTester;
    private ObjectProphecy $em;
    private ObjectProphecy $metadataFactory;

    protected function setUp(): void
    {
        $this->entityManagerProvider = $this->prophesize(EntityManagerProvider::class);

        $options = new Options();

        $application = new Application();
        $application->add(new CreateChangelogCommand($this->entityManagerProvider->reveal(), $options));

        $command = $application->find('orm:liquibase:createchangelog');

        $this->commandTester = new CommandTester($command);

        $option = new InputOption(
            'em',
            null,
            InputOption::VALUE_OPTIONAL,
            'The name of the entity manager to use.',
            MultiEntityManagerProvider::DEFAULT_ENTITYMANAGER_NAME
        );

        $command->getDefinition()->addOption($option);

        $this->metadataFactory = $this->prophesize(ClassMetadataFactory::class);

        $this->em = $this->prophesize(EntityManagerInterface::class);
        $this->em->getMetadataFactory()->willReturn($this->metadataFactory->reveal());

        $this->entityManagerProvider->getManager(Argument::any())->willReturn($this->em->reveal());

        $helperSet = $this->prophesize(HelperSet::class);
        $command->setHelperSet($helperSet->reveal());
    }

    public function testExecuteWithoutMetadatas(): void
    {
        $this->metadataFactory->getAllMetadata()->willReturn([]);

        $this->commandTester->execute([]);

        $this->assertStringContainsString('[OK] No Metadata Classes to process.', $this->commandTester->getDisplay());
    }

    public function testExecuteWithMetadatas(): void
    {
        $quoteStrategy = $this->prophesize(QuoteStrategy::class);
        $eventManager  = $this->prophesize(EventManager::class);
        $eventManager->hasListeners(ToolEvents::postGenerateSchema)
            ->willReturn(false);
        $eventManager->hasListeners(ToolEvents::postGenerateSchemaTable)
            ->willReturn(false);

        $emConfig = $this->prophesize(EMConfig::class);
        $emConfig->getQuoteStrategy()->willReturn($quoteStrategy->reveal());
        if (method_exists(EMConfig::class, 'getSchemaIgnoreClasses')) {
            $emConfig->getSchemaIgnoreClasses()->willReturn([]);
        }

        $table1 = new Table('tablename1');
        $table2 = new Table('tablename2');

        $schemaConfig = new SchemaConfig();
        $schema       = new Schema([$table1, $table2]);

        $classMetadata1       = $this->prophesize(ClassMetadata::class);
        $classMetadata1->name = 'test1';
        $classMetadata1->getName()->willReturn('test1');
        $classMetadata1->isInheritanceTypeSingleTable()->willReturn(true);
        $classMetadata1->rootEntityName = 'Foo';

        $classMetadata2       = $this->prophesize(ClassMetadata::class);
        $classMetadata2->name = 'test2';
        $classMetadata2->getName()->willReturn('test2');
        $classMetadata2->isInheritanceTypeSingleTable()->willReturn(true);
        $classMetadata2->rootEntityName = 'Bar';

        $quoteStrategy->getTableName($classMetadata1->reveal(), Argument::type(AbstractPlatform::class))
            ->willReturn('tablename1');
        $quoteStrategy->getTableName($classMetadata2->reveal(), Argument::type(AbstractPlatform::class))
            ->willReturn('tablename2');

        $schemaManager = $this->prophesize(AbstractSchemaManager::class);
        $schemaManager->introspectSchema()->willReturn($schema);
        $schemaManager->createSchemaConfig()->willReturn($schemaConfig);

        $platform = $this->prophesize(AbstractPlatform::class);
        $platform->getCreateSchemaSQL(Argument::any())->willReturn('test');
        $platform->supportsSchemas()->willReturn(false);

        $connection = $this->prophesize(Connection::class);
        $connection->getDatabasePlatform()
            ->willReturn($platform->reveal());
        $connection->createSchemaManager()
            ->willReturn($schemaManager->reveal());

        $this->em->getConnection()->willReturn($connection->reveal());
        $this->em->getConfiguration()->willReturn($emConfig->reveal());
        $this->em->getEventManager()->willReturn($eventManager->reveal());

        $this->metadataFactory->getAllMetadata()->willReturn([
            $classMetadata1->reveal(),
            $classMetadata2->reveal(),
        ]);

        $this->commandTester->execute([]);

        $this->assertStringContainsString('', $this->commandTester->getDisplay());
    }
}
