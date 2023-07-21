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
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\Compiler\ResolveParameterPlaceHoldersPass;
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

        // We replace drivers for configured manager with SolidMetadataDriver.
        foreach (array_keys($managers) as $connectionName) {
            $metadataDriverName = sprintf(
                'doctrine.orm.%s_metadata_driver',
                $connectionName,
            );

            if ($container->has($metadataDriverName)) {
                $definition = $container->getDefinition($metadataDriverName);
                $class = $container->getParameterBag()->resolveValue(
                    $definition->getClass(),
                );

                // SolidMetadataDriver replace drivers added on mapping driver
                // chain. If the driver definition is not a MappingDriverChain,
                // we pass.
                if ($class !== MappingDriverChain::class
                    && !in_array(
                        MappingDriverChain::class,
                        class_parents($class),
                    )
                ) {
                    continue;
                }

                // We replace the addDriver $nestedDriver parameter with a
                // SolidMetadataDriver.
                $calls = $definition->getMethodCalls();
                foreach ($calls as &$call) {
                    [$method, &$parameters] = $call;

                    if ($method === 'addDriver') {
                        $parameters[0] = new Definition(
                            SolidMetadataDriver::class, [
                                $config,
                                $parameters[0],
                            ],
                        );
                    }
                }

                $definition->setMethodCalls($calls);
            }
        }
    }

}