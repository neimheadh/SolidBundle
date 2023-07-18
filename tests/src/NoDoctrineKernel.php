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
use PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

/**
 *
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
class NoDoctrineKernel extends BaseKernel
{

    use MicroKernelTrait;

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
        $loader->load(function (ContainerBuilder $container) {
            $container->loadFromExtension('framework', [
                'http_method_override' => false,
                'test' => true,
            ]);
        });
    }

}