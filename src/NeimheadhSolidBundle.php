<?php

/*
 * This file is part of the NeimheadhSolid Bundle.
 *
 * (c) 2023 - present  Mathieu Wambre <contact@neimheadh.fr>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Neimheadh\SolidBundle;

use Exception;
use Neimheadh\SolidBundle\DependencyInjection\Compiler\DoctrineCompilerPass;
use Neimheadh\SolidBundle\DependencyInjection\Compiler\DoctrineOrmMappingCompiler;
use Neimheadh\SolidBundle\Doctrine\Mapping\Driver\SolidDoctrineEntityDriver;
use Symfony\Bridge\Doctrine\DependencyInjection\CompilerPass\RegisterMappingsPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use function dirname;

/**
 * SOLID development helper for symfony projects.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
class NeimheadhSolidBundle extends Bundle
{

    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function build(ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader(
            $container, new FileLocator(
                dirname(__DIR__) . '/config',
            )
        );

        class_exists(RegisterMappingsPass::class)
        && $this->loadDoctrine($container, $loader);
    }

    /**
     * {@inheritDoc}
     */
    public function getPath(): string
    {
        return dirname(__DIR__);
    }

    /**
     * Load doctrine compiler pass.
     *
     * @param ContainerBuilder $container Application container.
     * @param XmlFileLoader    $loader    Config file loader.
     *
     * @return void
     * @throws Exception
     */
    private function loadDoctrine(
        ContainerBuilder $container,
        XmlFileLoader $loader
    ): void {
        $container->addCompilerPass(new DoctrineOrmMappingCompiler());
    }

}
