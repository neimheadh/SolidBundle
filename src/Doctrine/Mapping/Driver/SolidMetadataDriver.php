<?php
/*
 * This file is part of the NeimheadhSolid Bundle.
 *
 * (c) 2023 - present  Mathieu Wambre <contact@neimheadh.fr>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Neimheadh\SolidBundle\Doctrine\Mapping\Driver;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use ReflectionClass;
use ReflectionException;

/**
 * Doctrine solid metadata driver.
 *
 * This driver decorate the application doctrine drivers in order to add
 * SOLID class metadata parameters to application doctrine entities.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
class SolidMetadataDriver implements MappingDriver
{

    /**
     * @param array         $config Bundle doctrine configuration.
     * @param MappingDriver $driver Base driver.
     */
    public function __construct(
        private readonly array $config,
        private readonly MappingDriver $driver
    ) {
    }

    /**
     * {@inheritDoc}
     *
     * Add class metadata information not already configured by the decorated
     * driver.
     *
     * @throws ReflectionException|MappingException
     */
    public function loadMetadataForClass(
        string $className,
        ClassMetadata $metadata
    ) {
        $this->driver->loadMetadataForClass($className, $metadata);

        if ($metadata instanceof ClassMetadataInfo) {
            $class = new ReflectionClass($className);
            foreach ($this->config as $interface => $config) {
                if ($class->implementsInterface($interface)) {
                    $this->configureEntity($metadata, $config);
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getAllClassNames(): array
    {
        return $this->driver->getAllClassNames();
    }

    /**
     * {@inheritDoc}
     */
    public function isTransient(string $className): bool
    {
        return $this->driver->isTransient($className);
    }

    /**
     * Configure entity metadata.
     *
     * @param ClassMetadataInfo $metadata Entity class metadata.
     * @param array             $config   Solid bundle interface configuration.
     *
     * @return void
     * @throws MappingException
     * @throws ReflectionException
     */
    private function configureEntity(
        ClassMetadataInfo $metadata,
        array $config,
    ): void {
        // Add identifier if configured for the current interface.
        if (!empty($config['identifier'])
            && empty($metadata->getIdentifier())
        ) {
            $metadata->setIdentifier($config['identifier']);
        }

        // Set id generation strategy if not configured.
        if ($config['generation_strategy'] !== null
            && $metadata->idGenerator === null
        ) {
            $metadata->setIdGeneratorType($config['generation_strategy']);
        }

        // Configure current interface columns.
        if (!empty($config['columns'])) {
            foreach ($config['columns'] as $fieldName => $fieldConfig) {
                $fieldName = $fieldConfig['fieldName'] ?? $fieldName;

                $fieldConfig['fieldName'] = $fieldName;
                $fieldConfig['columnName'] = $fieldConfig['columnName']
                    ?? $fieldName;

                if (!$metadata->hasField($fieldName)) {
                    $metadata->fieldMappings[$fieldName] = $fieldConfig;
                    $metadata->fieldNames[$fieldName] = $fieldName;
                }
            }
        }

        // Configure entity listeners.
        if (!empty($config['listeners'])) {
            foreach ($config['listeners'] as $eventName => $listener) {
                $class = new ReflectionClass($listener['class']);
                $method = ($listener['method'] ?? null)
                    ?: ($class->hasMethod($eventName)
                        ? $eventName
                        : '__invoke'
                    );

                $metadata->addEntityListener(
                    $eventName,
                    $class->getName(),
                    $method,
                );
            }
        }
    }

}