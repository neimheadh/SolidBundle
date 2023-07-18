<?php
/*
 * This file is part of the NeimheadhSolid Bundle.
 *
 * (c) 2023 - present  Mathieu Wambre <contact@neimheadh.fr>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Neimheadh\SolidBundle\Tests;

use Exception;
use Neimheadh\SolidBundle\Tests\DependencyInjection\Compiler\TestContainerCompiler;
use PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Test kernel.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
class Kernel extends NoDoctrineKernel
{

    /**
     * @param string $environment               Application environment.
     * @param bool   $debug                     Is kernel debug enabled.
     * @param bool   $removeEntityManagerDriver Remove entity manager driver
     *                                          from container.
     * @param bool   $removeClassMetadataDriver Remove class metadata driver
     *                                          from container.
     */
    public function __construct(
        string $environment,
        bool $debug,
        private readonly bool $removeEntityManagerDriver = false,
        private readonly bool $removeClassMetadataDriver = false,
    ) {
        parent::__construct($environment, $debug);
    }

    /**
     * {@inheritDoc}
     */
    public function getProjectDir(): string
    {
        return dirname(__DIR__);
    }

    /**
     * {@inheritDoc}
     */
    protected function getContainerBaseClass(): string
    {
        return MockerContainer::class;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function registerContainerConfiguration(
        LoaderInterface $loader
    ): void {
        parent::registerContainerConfiguration($loader);

        $loader->load(function (ContainerBuilder $container) {
            $container->loadFromExtension('doctrine', [
                'dbal' => [
                    'driver' => 'pdo_sqlite',
                    'path' => dirname(__DIR__) . '/var/cache/test.db',
                ],
                'orm' => [
                    'auto_generate_proxy_classes' => true,
                    'entity_managers' => [
                        'default' => [
                            'auto_mapping' => true,
                            'mappings' => [
                                'Tests' => [
                                    'is_bundle' => false,
                                    'dir' => __DIR__ . '/Entity',
                                    'prefix' => 'Neimheadh\SolidBundle\Tests',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            $container->addCompilerPass(new TestContainerCompiler(
                removeEntityManagerDriver: $this->removeEntityManagerDriver,
                removeClassMetadataDriver: $this->removeClassMetadataDriver,
            ), PassConfig::TYPE_OPTIMIZE);
        });
    }

}