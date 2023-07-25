<?php
/*
 * This file is part of the NeimheadhSolid Bundle.
 *
 * (c) 2023 - present  Mathieu Wambre <contact@neimheadh.fr>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Neimheadh\SolidBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Neimheadh Solid Bundle extension.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
class NeimheadhSolidExtension extends Extension
{

    /**
     * Doctrine configuration parameter name.
     */
    public const PARAMETER_CONFIG_DOCTRINE = 'neimheadh.solid.config.doctrine';

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $this->loadServices($configs, $container);
        $this->loadConfiguration($configs, $container);
    }

    /**
     * Load bundle configuration.
     *
     * @param array[]          $configs   Environment configurations.
     * @param ContainerBuilder $container Application container.
     *
     * @return void
     */
    private function loadConfiguration(
        array $configs,
        ContainerBuilder $container,
    ): void {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(
            self::PARAMETER_CONFIG_DOCTRINE,
            $config['doctrine'],
        );
    }

    /**
     * Load bundle services.
     *
     * @param array[]          $configs   Environment configurations.
     * @param ContainerBuilder $container Application container.
     *
     * @return void
     * @throws Exception
     */
    private function loadServices(
        array $configs,
        ContainerBuilder $container,
    ): void {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(
                dirname(__DIR__, 2) . '/config',
            ),
        );

        $loader->load('doctrine.xml');
    }

}
