<?php
/*
 * This file is part of the NeimheadhSolid Bundle.
 *
 * (c) 2023 - present  Mathieu Wambre <contact@neimheadh.fr>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

$doctrine = $this->options['doctrine'] ?? true;

$bundles = [
    Neimheadh\SolidBundle\NeimheadhSolidBundle::class => ['all' => true],
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
];

$doctrine && $bundles[Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class] = ['all' => true];

return $bundles;