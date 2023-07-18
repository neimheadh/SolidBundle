<?php
/*
 * This file is part of the NeimheadhSolid Bundle.
 *
 * (c) 2023 - present  Mathieu Wambre <contact@neimheadh.fr>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Neimheadh\SolidBundle\Tests\DependencyInjection\Compiler;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Test container compiler pass.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
class TestContainerCompiler implements CompilerPassInterface
{

    /**
     * @param bool $removeEntityManagerDriver Remove entity manager driver from
     *                                        container.
     * @param bool $removeClassMetadataDriver Remove class metadata driver from
     *                                        container.
     */
    public function __construct(
        private readonly bool $removeEntityManagerDriver = false,
        private readonly bool $removeClassMetadataDriver = false,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
    }

}