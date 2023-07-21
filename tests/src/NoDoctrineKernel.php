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
use Neimheadh\SolidBundle\NeimheadhSolidBundle;
use Neimheadh\SolidBundle\Tests\Entity\UserEntity;
use PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

/**
 * Kernel with no doctrine configured.
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
     */
    public function registerBundles(): iterable
    {
        return [
            new NeimheadhSolidBundle(),
            new FrameworkBundle(),
            new TwigBundle(),
            new SecurityBundle(),
        ];
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
            $container->setParameter('kernel.secret', 'secret');

            $container->loadFromExtension('framework', [
                'http_method_override' => false,
                'test' => true,
                'router' => [
                    'utf8' => true,
                    'resource' => '.',
                ],
            ]);

            $container->loadFromExtension('security', [
                'firewalls' => [
                    'main' => [
                        'lazy' => true,
                    ],
                ],
                'password_hashers' => [
                    UserEntity::class => 'auto',
                ],
            ]);
        });
    }

}