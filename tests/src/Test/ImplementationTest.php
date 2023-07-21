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

use Doctrine\Bundle\DoctrineBundle\Mapping\MappingDriver;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\Mapping\Driver\StaticPHPDriver;
use Exception;
use Neimheadh\SolidBundle\DependencyInjection\Compiler\DoctrineOrmMappingCompiler;
use Neimheadh\SolidBundle\Tests\Doctrine\FakeMappingDriver;
use Neimheadh\SolidBundle\Tests\Entity\GenericEntity;
use Neimheadh\SolidBundle\Tests\NoDoctrineKernel;
use Sonata\UserBundle\Entity\BaseUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Throwable;

/**
 * Bundle implementation test.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
class ImplementationTest extends WebTestCase
{

    /**
     * Test the doctrine driver override is well-done.
     *
     * @return void
     * @throws Exception
     */
    public function testDoctrineDriverOverride(): void
    {
        $container = static::getContainer();

        // We check the driver was override.
        /** @var Registry $doctrine */
        $doctrine = $container->get('doctrine');
        /** @var EntityManager $manager */
        $manager = $doctrine->getManager();
        /** @var MappingDriver $mappingDriver */
        $mappingDriver = $manager->getConfiguration()->getMetadataDriverImpl();

        $this->assertSame([
            GenericEntity::class,
            BaseUser::class,
        ], $mappingDriver->getAllClassNames());
    }

    /**
     * Test bundle implementation on a kernel without doctrine does not throw
     * any error.
     *
     * @return void
     */
    public function testNoDoctrineImplementation(): void
    {
        $kernel = static::bootKernel();
        $test = new NoDoctrineKernel(
            $kernel->getEnvironment(),
            $kernel->isDebug()
        );

        $e = null;
        try {
            $test->boot();
        } catch (Throwable $e) {
        }

        $this->assertNull($e);
    }

    public function testExoticMappingDriverLeftUntouched(): void
    {
        $container = static::getContainer();
        $containerBuilder = new ContainerBuilder();
        $doctrine = new Registry(
            $containerBuilder,
            $container->get('doctrine')->getConnections(),
            [
                'default' => $container->get('doctrine')->getManager(),
            ],
            $container->get('doctrine')->getDefaultConnectionName(),
            $container->get('doctrine')->getDefaultManagerName(),
        );

        $containerBuilder->addCompilerPass(new DoctrineOrmMappingCompiler());
        $containerBuilder->setParameter(
            'neimheadh.solid.config.doctrine',
            $container->getParameter('neimheadh.solid.config.doctrine')
        );
        $containerBuilder->set('doctrine', $doctrine);
        $containerBuilder->setDefinition(
            'doctrine.orm.default_metadata_driver',
            new Definition(FakeMappingDriver::class),
        );
        $containerBuilder->getDefinition('doctrine.orm.default_metadata_driver')
            ->addMethodCall(
                'addDriver',
                [new Definition(StaticPHPDriver::class, [[]]), 'App\\Test'],
            )->setPublic(true);
        $containerBuilder->compile();

        // We test the doctrine.orm.default_metadata_driver first driver is
        // still a StaticPHPDriver object. If doctrine.orm.default_metadata_driver
        // was a MappingDriverChain object, it would be transformed in a
        // SolidMetadataDriver.
        $this->assertInstanceOf(
            StaticPHPDriver::class,
            $containerBuilder->get('doctrine.orm.default_metadata_driver')
                ->getDrivers()['App\\Test'],
        );
    }

}