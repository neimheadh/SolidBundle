<?php
/*
 * This file is part of the NeimheadhSolid Bundle.
 *
 * (c) 2023 - present  Mathieu Wambre <contact@neimheadh.fr>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Neimheadh\SolidBundle\DependencyInjection\Compiler;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Exception;
use Neimheadh\SolidBundle\DependencyInjection\NeimheadhSolidExtension;
use Neimheadh\SolidBundle\Doctrine\Mapping\Driver\SolidMetadataDriver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Solid bundle doctrine mapping compiler.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
class DoctrineOrmMappingCompiler implements CompilerPassInterface
{

    /**
     * {@inheritDoc}
     *
     * Browse the list of application doctrine drivers and decorate them with
     * SolidMetadataDriver.
     *
     * @throws Exception
     */
    public function process(ContainerBuilder $container): void
    {
        $config = $container->getParameter(
            NeimheadhSolidExtension::PARAMETER_CONFIG_DOCTRINE
        );

        // If doctrine service is not configured, we ignore the ORM auto-mapping
        // processor.
        if (!$container->has('doctrine')) {
            return;
        }

        /** @var ManagerRegistry $doctrine */
        $doctrine = $container->get('doctrine');
        $managers = $doctrine->getManagerNames();

        foreach ($managers as $name => $manager) {
            $driver = sprintf('doctrine.orm.%s_metadata_driver', $name);

            if ($container->has($driver)) {
                /** @var MappingDriverChain $service */
                $definition = $container->getDefinition($driver);

                foreach ($definition->getMethodCalls() as $call) {
                    if ($call[0] === 'addDriver') {
                        $definition->removeMethodCall($call[0]);
                        $definition->addMethodCall('addDriver', [
                            new Definition(SolidMetadataDriver::class, [
                                $config,
                                $call[1][0],
                            ]),
                            $call[1][1],
                        ]);
                    }
                }
            }
        }
    }

}