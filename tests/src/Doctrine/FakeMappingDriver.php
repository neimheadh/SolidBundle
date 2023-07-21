<?php
/*
 * This file is part of the NeimheadhSolid Bundle.
 *
 * (c) 2023 - present  Mathieu Wambre <contact@neimheadh.fr>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Neimheadh\SolidBundle\Tests\Doctrine;

use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;

/**
 * Fake mapping driver to test addDriver calls.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
class FakeMappingDriver implements MappingDriver
{
    /** @var array<string, MappingDriver> */
    private array $drivers = [];

    /**
     * Adds a nested driver.
     *
     * @return void
     */
    public function addDriver(MappingDriver $nestedDriver, string $namespace)
    {
        $this->drivers[$namespace] = $nestedDriver;
    }

    /**
     * Get nested drivers.
     *
     * @return array
     */
    public function getDrivers(): array
    {
        return $this->drivers;
    }

    /**
     * {@inheritDoc}
     */
    public function loadMetadataForClass(
        string $className,
        ClassMetadata $metadata
    ): void {
    }

    /**
     * {@inheritDoc}
     */
    public function getAllClassNames(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function isTransient(string $className): bool
    {
        return false;
    }

}