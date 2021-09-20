<?php

declare(strict_types=1);

namespace Fabiang\DoctrineMigrationsLiquibase\Command;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Argument;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\Configuration as EMConfig;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\QuoteStrategy;
use Doctrine\DBAL\Schema\SchemaConfig;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\Tools\ToolEvents;
use Doctrine\Common\EventManager;

/**
 * @coversDefaultClass Fabiang\DoctrineMigrationsLiquibase\Command\CreateChangelogCommand
 */
final class CreateChangelogCommandTest extends TestCase
{

    use ProphecyTrait;

    private CommandTester $commandTester;
    private ObjectProphecy $em;
    private ObjectProphecy $metadataFactory;

    protected function setUp(): void
    {
        $application = new Application();
        $application->add(new CreateChangelogCommand());

        $command = $application->find('orm:liquibase:createchangelog');

        $this->commandTester = new CommandTester($command);

        $this->metadataFactory = $this->prophesize(ClassMetadataFactory::class);

        $this->em = $this->prophesize(EntityManagerInterface::class);
        $this->em->getMetadataFactory()->willReturn($this->metadataFactory->reveal());

        $emHelper = $this->prophesize(EntityManagerHelper::class);
        $emHelper->getEntityManager()->willReturn($this->em->reveal());

        $helperSet = $this->prophesize(HelperSet::class);
        $helperSet->has('em')->willReturn(true);
        $helperSet->get('em')->willReturn($emHelper->reveal());
        $command->setHelperSet($helperSet->reveal());
    }

    /**
     * @test
     * @covers ::execute
     * @covers ::configure
     */
    public function executeWithoutMetadatas(): void
    {
        $this->metadataFactory->getAllMetadata()->willReturn([]);

        $this->commandTester->execute([]);

        $this->assertStringContainsString('[OK] No Metadata Classes to process.', $this->commandTester->getDisplay());
    }

    /**
     * @test
     * @covers ::execute
     * @covers ::executeSchemaCommand
     * @covers ::configure
     */
    public function executeWithMetadatas(): void
    {
        $quoteStrategy = $this->prophesize(QuoteStrategy::class);
        $eventManager  = $this->prophesize(EventManager::class);
        $eventManager->hasListeners(ToolEvents::postGenerateSchema)
            ->willReturn(false);
        $eventManager->hasListeners(ToolEvents::postGenerateSchemaTable)
            ->willReturn(false);

        $emConfig = $this->prophesize(EMConfig::class);
        $emConfig->getQuoteStrategy()->willReturn($quoteStrategy->reveal());

        $table1 = new Table('tablename1');
        $table2 = new Table('tablename2');

        $schemaConfig = new SchemaConfig();
        $schema       = new Schema([$table1, $table2]);

        $classMeta1        = new ClassMetadata('entity1');
        $classMeta1->table = 'tablename1';
        $classMeta2        = new ClassMetadata('entity2');
        $classMeta2->table = 'tablename2';

        $quoteStrategy->getTableName($classMeta1, Argument::type(AbstractPlatform::class))
            ->willReturn('tablename1');
        $quoteStrategy->getTableName($classMeta2, Argument::type(AbstractPlatform::class))
            ->willReturn('tablename2');

        $schemaManager = $this->prophesize(AbstractSchemaManager::class);
        $schemaManager->createSchema()->willReturn($schema);
        $schemaManager->createSchemaConfig()->willReturn($schemaConfig);

        $platform = $this->prophesize(AbstractPlatform::class);
        $platform->getCreateSchemaSQL(Argument::any())->willReturn('test');
        $platform->supportsSchemas()->willReturn(false);
        $platform->canEmulateSchemas()->willReturn(false);

        $connection = $this->prophesize(Connection::class);

        $connection->getDatabasePlatform()
            ->willReturn($platform->reveal());
        $connection->getSchemaManager()
            ->willReturn($schemaManager->reveal());

        $this->em->getConnection()->willReturn($connection->reveal());
        $this->em->getConfiguration()->willReturn($emConfig->reveal());
        $this->em->getEventManager()->willReturn($eventManager->reveal());

        $this->metadataFactory->getAllMetadata()->willReturn([
            $classMeta1,
            $classMeta2,
        ]);

        $this->commandTester->execute([]);

        $this->assertStringContainsString('', $this->commandTester->getDisplay());
    }

}
