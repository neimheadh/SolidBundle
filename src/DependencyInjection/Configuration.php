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

use Neimheadh\SolidBundle\DependencyInjection\Configuration\DoctrineConfiguration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Neimheadh Solid Bundle configuration.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class Configuration implements ConfigurationInterface
{

    /**
     * Invalid type error.
     */
    private const INVALID_TYPE = 'Invalid name type "%s".';

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('neimheadh_solid');

        $treeBuilder->getRootNode()->children()->append(
            (new DoctrineConfiguration())->getConfigTreeBuilder()->getRootNode()
        );

        return $treeBuilder;
    }

}
