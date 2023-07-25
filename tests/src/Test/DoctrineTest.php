<?php
/*
 * This file is part of the NeimheadhSolid Bundle.
 *
 * (c) 2023 - present  Mathieu Wambre <contact@neimheadh.fr>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Neimheadh\SolidBundle\Tests\Test;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\ORM\Id\IdentityGenerator;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Exception;
use Neimheadh\SolidBundle\Doctrine\EventListener\Date\CreatedEntityListener;
use Neimheadh\SolidBundle\Doctrine\EventListener\Date\UpdatedEntityListener;
use Neimheadh\SolidBundle\Tests\Entity\DefaultOverrideEntity;
use Neimheadh\SolidBundle\Tests\Entity\GenericEntity;
use Neimheadh\SolidBundle\Tests\Entity\OwnerEntity;
use Sonata\UserBundle\Entity\BaseUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Doctrine implementation tests.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
class DoctrineTest extends WebTestCase
{

    /**
     * Test date fields are well-implemented.
     *
     * @return void
     * @throws Exception
     */
    public function testDateFieldsImplementation(): void
    {
        $container = static::getContainer();
        $now = new DateTime();

        /** @var ManagerRegistry $doctrine */
        $doctrine = $container->get('doctrine');
        /** @var EntityManagerInterface $em */
        $em = $doctrine->getManager('default');

        $metadata = $em->getClassMetadata(GenericEntity::class);

        $this->assertTrue($metadata->hasField('creationDate'));
        $this->assertTrue($metadata->hasField('updateDate'));

        $this->assertSame([
            'type' => 'datetime_immutable',
            'length' => null,
            'options' => ['default' => 'CURRENT_TIMESTAMP'],
            'unique' => false,
            'nullable' => false,
            'fieldName' => 'creationDate',
            'columnName' => 'creationDate',
        ], $metadata->fieldMappings['creationDate']);
        $this->assertSame([
            'type' => 'datetime',
            'length' => null,
            'options' => [],
            'unique' => false,
            'nullable' => true,
            'fieldName' => 'updateDate',
            'columnName' => 'updateDate',
        ], $metadata->fieldMappings['updateDate']);

        $this->assertContains([
            'class' => CreatedEntityListener::class,
            'method' => '__invoke',
        ], $metadata->entityListeners[Events::prePersist]);
        $this->assertContains([
            'class' => UpdatedEntityListener::class,
            'method' => 'preUpdate',
        ], $metadata->entityListeners[Events::preUpdate]);

        $entity = $this->newGenericEntity();
        $this->assertNull($entity->getCreationDate());
        $em->persist($entity);
        $em->flush();
        $this->assertGreaterThanOrEqual(
            $now->getTimestamp(),
            $entity->getCreationDate()?->getTimestamp()
        );
        $this->assertNull($entity->getUpdateDate());
        $entity->setName($entity->getName() . ' -- update');
        $em->persist($entity);
        $em->flush();
        $this->assertGreaterThanOrEqual(
            $now->getTimestamp(),
            $entity->getUpdateDate()?->getTimestamp()
        );
    }

    /**
     * Test generic fields are well implemented.
     *
     * @return void
     * @throws Exception
     */
    public function testGenericFieldsImplementation(): void
    {
        $container = static::getContainer();

        /** @var ManagerRegistry $doctrine */
        $doctrine = $container->get('doctrine');
        /** @var EntityManagerInterface $em */
        $em = $doctrine->getManager('default');

        /** @var ClassMetadataInfo $metadata */
        $metadata = $doctrine->getManager('default')->getClassMetadata(
            GenericEntity::class
        );

        $this->assertTrue($metadata->hasField('name'));
        $this->assertTrue($metadata->hasField('description'));

        $this->assertSame([
            'type' => 'text',
            'length' => null,
            'options' => [],
            'unique' => false,
            'nullable' => true,
            'fieldName' => 'description',
            'columnName' => 'description',
        ], $metadata->fieldMappings['description']);
        $this->assertSame([
            'type' => 'string',
            'length' => 256,
            'options' => [],
            'unique' => false,
            'nullable' => false,
            'fieldName' => 'name',
            'columnName' => 'name',
        ], $metadata->fieldMappings['name']);

        $name = sprintf('Name %s', date('YmdHis'));
        $description = sprintf('Description %s %s', uniqid(), date('YmdHis'));
        $entity = $this->newGenericEntity();

        $entity->setDescription($description);
        $entity->setName($name);

        $em->persist($entity);
        $em->flush();
        $em->clear();

        $entity = $em->find(GenericEntity::class, 1);
        $this->assertSame($name, $entity->getName());
        $this->assertSame($description, $entity->getDescription());
    }

    /**
     * Test join fields are well implemented.
     *
     * @return void
     * @throws Exception
     */
    public function testJoinFieldsImplementation(): void
    {
        $container = static::getContainer();

        /** @var ManagerRegistry $doctrine */
        $doctrine = $container->get('doctrine');
        /** @var EntityManagerInterface $em */
        $em = $doctrine->getManager('default');

        /** @var ClassMetadataInfo $metadata */
        $metadata = $doctrine->getManager('default')->getClassMetadata(
            OwnerEntity::class
        );
        $this->assertTrue($metadata->hasField('isDefault'));
        $this->assertSame([
            'type' => 'boolean',
            'length' => null,
            'options' => ['default' => null],
            'unique' => true,
            'nullable' => true,
            'fieldName' => 'isDefault',
            'columnName' => 'isDefault',
        ], $metadata->fieldMappings['isDefault']);

        /** @var ClassMetadataInfo $metadata */
        $metadata = $doctrine->getManager('default')->getClassMetadata(
            DefaultOverrideEntity::class
        );
        $this->assertTrue($metadata->hasField('isDefault'));
        $this->assertSame([
            'fieldName' => 'isDefault',
            'type' => 'boolean',
            'scale' => null,
            'length' => null,
            'unique' => false,
            'nullable' => true,
            'precision' => null,
            'options' => ['default' => null],
            'columnName' => 'isDefault',
        ], $metadata->fieldMappings['isDefault']);

        // Test default system works with unit of work.
        $concurrent = new OwnerEntity();
        $concurrent->setIsDefault();
        $em->persist($concurrent);
        $owner = new OwnerEntity();
        $owner->setIsDefault();
        $em->persist($owner);
        $generic = $this->newGenericEntity();
        $em->persist($generic);
        $em->flush();

        $this->assertSame(1, $concurrent->getId());
        $this->assertSame(2, $owner->getId());
        $this->assertFalse($concurrent->isDefault());
        $this->assertTrue($owner->isDefault());
        $this->assertSame($owner, $generic->getOwner());

        // Test auto-set works with empty manager uow.
        $em->clear();
        $default = new DefaultOverrideEntity();
        $default->setIsDefault();
        $em->persist($default);
        $owner = $em->find(OwnerEntity::class, 2);
        $concurrent = $em->find(OwnerEntity::class, 1);
        $generic = $this->newGenericEntity();
        $em->persist($generic);
        $em->flush();
        $this->assertSame($owner, $generic->getOwner());
        $this->assertSame($default, $generic->getDefaultOverride());

        $generic = $this->newGenericEntity();
        $generic->setOwner($concurrent);
        $em->persist($generic);
        $em->flush();
        $this->assertSame($concurrent, $generic->getOwner());


        $default = new DefaultOverrideEntity();
        $em->persist($default);
        $em->flush();
        $em->createQueryBuilder()
            ->update(DefaultOverrideEntity::class, 'd')
            ->set('d.isDefault', true)
            ->getQuery()
            ->execute();
        $this->assertCount(
            2,
            $em->createQueryBuilder()
                ->select(['d'])
                ->from(DefaultOverrideEntity::class, 'd')
                ->where('d.isDefault = :default')
                ->getQuery()
                ->execute(['default' => true])
        );
        $em->clear();
        $generic = $this->newGenericEntity();
        $em->persist($generic);
        $em->flush();
        $this->assertSame(
            $em->find(DefaultOverrideEntity::class, 1),
            $generic->getDefaultOverride(),
        );
    }

    /**
     * Test SolidMetadataDriver.
     *
     * @return void
     * @throws Exception
     */
    public function testSolidMetadataDriver(): void
    {
        $container = static::getContainer();

        /** @var MappingDriver $metadataDriver */
        $metadataDriver = $container->get(
            'doctrine.orm.default_metadata_driver'
        );

        // We check doctrine & mapping drivers meta-methods have the same
        // behavior.
        $this->assertSame(
            [
                GenericEntity::class,
                DefaultOverrideEntity::class,
                OwnerEntity::class,
                BaseUser::class,
            ],
            $metadataDriver->getAllClassNames(),
        );

        foreach ($metadataDriver->getAllClassNames() as $className) {
            $this->assertSame(
                false,
                $metadataDriver->isTransient($className),
            );
        }
    }

    /**
     * Test doctrine implementation.
     *
     * @return void
     * @throws Exception
     */
    public function testUniquePrimaryIdImplementation(): void
    {
        $container = static::getContainer();

        /** @var ManagerRegistry $doctrine */
        $doctrine = $container->get('doctrine');
        /** @var EntityManagerInterface $em */
        $em = $doctrine->getManager('default');

        $metadata = $em->getClassMetadata(GenericEntity::class);
        $this->assertSame(['id'], $metadata->getIdentifier());
        $this->assertTrue($metadata->hasField('id'));
        $this->assertSame([
            'type' => 'integer',
            'length' => null,
            'options' => [],
            'unique' => false,
            'nullable' => false,
            'fieldName' => 'id',
            'columnName' => 'id',
        ], $metadata->fieldMappings['id']);
        $this->assertInstanceOf(
            IdentityGenerator::class,
            $metadata->idGenerator
        );

        $generic = $this->newGenericEntity();
        $em->persist($generic);
        $em->flush();
        $this->assertEquals(1, $generic->getId());
    }

    /**
     * {@inheritDoc}
     * @throws Exception
     */
    protected function setUp(): void
    {
        static::bootKernel();

        $container = static::getContainer();

        /** @var ManagerRegistry $doctrine */
        $doctrine = $container->get('doctrine');
        /** @var EntityManagerInterface $em */
        $em = $doctrine->getManager('default');

        $manager = $em->getConnection()->createSchemaManager();
        $schema = new SchemaTool($em);

        foreach (
            [
                GenericEntity::class,
                OwnerEntity::class,
                DefaultOverrideEntity::class,
            ] as $entity
        ) {
            $metadata = $em->getClassMetadata($entity);
            $manager->tablesExist($metadata->getTableName())
            && $schema->dropSchema([$metadata]);
            $schema->createSchema([$metadata]);
        }
    }

    /**
     * Create new generic entity ready to persist.
     *
     * @return GenericEntity
     */
    private function newGenericEntity(): GenericEntity
    {
        $entity = new GenericEntity();

        $entity->setName(sprintf('Test %s', date('Y-m-d H:i:s')));

        return $entity;
    }

}