<?php
/*
 * This file is part of the NeimheadhSolid Bundle.
 *
 * (c) 2023 - present  Mathieu Wambre <contact@neimheadh.fr>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Neimheadh\SolidBundle\DependencyInjection\Configuration;

use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Neimheadh\SolidBundle\Doctrine\Entity\Date\CreatedEntityInterface;
use Neimheadh\SolidBundle\Doctrine\Entity\Date\UpdatedEntityInterface;
use Neimheadh\SolidBundle\Doctrine\Entity\Generic\DescribedEntityInterface;
use Neimheadh\SolidBundle\Doctrine\Entity\Generic\NamedEntityInterface;
use Neimheadh\SolidBundle\Doctrine\Entity\Index\UniquePrimaryEntityInterface;
use Neimheadh\SolidBundle\Doctrine\Entity\Join\DefaultJointEntityInterface;
use Neimheadh\SolidBundle\Doctrine\EventListener\Date\CreatedEntityListener;
use Neimheadh\SolidBundle\Doctrine\EventListener\Date\UpdatedEntityListener;
use Neimheadh\SolidBundle\Doctrine\EventListener\Join\DefaultJointEntityListener;
use Neimheadh\SolidBundle\Exception\ConfigurationException;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * ORM configuration tree builder.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
class DoctrineConfiguration implements ConfigurationInterface
{

    /**
     * Doctrine generation strategies.
     */
    public const DOCTRINE_GENERATION_STRATEGIES = [
        'AUTO' => ClassMetadataInfo::GENERATOR_TYPE_AUTO,
        'SEQUENCE' => ClassMetadataInfo::GENERATOR_TYPE_SEQUENCE,
        'IDENTITY' => ClassMetadataInfo::GENERATOR_TYPE_IDENTITY,
    ];

    /**
     * Doctrine types.
     */
    private const DOCTRINE_TYPES = [
        'array',
        'ascii_string',
        'bigint',
        'binary',
        'blob',
        'boolean',
        'date',
        'date_immutable',
        'dateinterval',
        'datetime',
        'datetime_immutable',
        'datetimetz',
        'datetimetz_immutable',
        'decimal',
        'float',
        'guid',
        'integer',
        'json',
        'object',
        'simple_array',
        'smallint',
        'string',
        'text',
        'time',
        'time_immutable',
    ];

    /**
     * Fields node.
     *
     * @var ArrayNodeDefinition
     */
    private ArrayNodeDefinition $root;

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('doctrine');

        $this->root = $treeBuilder
            ->getRootNode()
            ->addDefaultsIfNotSet();

        // Generic fields.
        $this->configure(
            DescribedEntityInterface::class,
            [
                'columns' => [
                    'description' => [
                        'type' => 'text',
                        'nullable' => true,
                    ]
                ]
            ]
        )->configure(
            NamedEntityInterface::class,
            [
                'columns' => [
                    'name' => [
                        'type' => 'string',
                        'length' => 256,
                    ],
                ]
            ]
        );

        // Index fields.
        $this->configure(
            UniquePrimaryEntityInterface::class,
            [
                'columns' => [
                    'id' => [
                        'type' => 'integer',
                    ],
                ],
                'identifier' => ['id'],
                'generation_strategy' => ClassMetadataInfo::GENERATOR_TYPE_AUTO,
            ]
        );

        // Date fields.
        $this->configure(
            CreatedEntityInterface::class,
            [
                'columns' => [
                    'creationDate' => [
                        'type' => 'datetime_immutable',
                        'options' => ['default' => 'CURRENT_TIMESTAMP'],
                    ]
                ],
                'listeners' => [
                    Events::prePersist => [
                        'class' => CreatedEntityListener::class,
                    ],
                ],
            ],
        )->configure(
            UpdatedEntityInterface::class,
            [
                'columns' => [
                    'updateDate' => [
                        'type' => 'datetime',
                        'nullable' => true,
                    ],
                ],
                'listeners' => [
                    Events::preUpdate => [
                        'class' => UpdatedEntityListener::class,
                    ],
                ],
            ],
        );

        // Join fields.
        $this->configure(
            DefaultJointEntityInterface::class,
            [
                'columns' => [
                    'isDefault' => [
                        'type' => 'boolean',
                        'unique' => true,
                        'nullable' => true,
                        'options' => ['default' => null],
                    ],
                ],
            ],
        );

        return $treeBuilder;
    }

    /**
     * Add column configuration tree.
     *
     * @param ArrayNodeDefinition $root   Column root node.
     * @param array               $config Column configuration.
     *
     * @return void
     */
    private function addColumn(
        ArrayNodeDefinition $root,
        array $config,
    ): void {
        $root
            ->addDefaultsIfNotSet()
            ->children()
                ->enumNode('type')
                    ->values(self::DOCTRINE_TYPES)
                    ->defaultValue($config['type'] ?? null)
                ->end()

                ->integerNode('length')
                    ->validate()
                        ->ifTrue(fn (?int $value) => $value && $value < 0)
                        ->thenInvalid('Length should be greater than 0.')
                    ->end()
                    ->defaultValue($config['length'] ?? null)
                ->end()

                ->arrayNode('options')
                    ->scalarPrototype()->end()
                    ->defaultValue($config['options'] ?? [])
                ->end()

                ->booleanNode('unique')
                    ->defaultValue($config['unique'] ?? false)
                ->end()

                ->booleanNode('nullable')
                    ->defaultValue($config['nullable'] ?? false)
                ->end()
            ->end();
    }

    /**
     * Add entity configuration tree.
     *
     * @param ArrayNodeDefinition $root    Entity root node.
     * @param array               $config  Entity configuration.
     *
     * @return void
     */
    private function addEntity(
        ArrayNodeDefinition $root,
        array $config,
    ): void {
        $root
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('listeners')
                    ->useAttributeAsKey('event')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('event')->isRequired()->end()
                            ->scalarNode('class')->isRequired()->end()
                            ->scalarNode('method')->defaultNull()->end()
                        ->end()
                    ->end()
                    ->defaultValue($config['listeners'] ?? [])
                ->end()

                ->arrayNode('identifier')
                    ->scalarPrototype()->end()
                    ->defaultValue($config['identifier'] ?? [])
                ->end()

                ->scalarNode('generation_strategy')
                    ->beforeNormalization()
                        ->ifString()
                        ->then(fn (string $strategy) => array_key_exists(
                                $strategy,
                                self::DOCTRINE_GENERATION_STRATEGIES,
                            ) ? self::DOCTRINE_GENERATION_STRATEGIES[$strategy]
                            : throw new ConfigurationException(
                                sprintf(
                                    'Unknown %s generation strategy.',
                                    $strategy,
                                ),
                            ),
                        )
                    ->end()
                    ->defaultValue($config['generation_strategy'] ?? null)
                ->end()
            ->end();

        $columnList = $root->children()
            ->arrayNode('columns')
            ->addDefaultsIfNotSet();
        foreach ($config['columns'] ?? [] as $column => $config) {
            $this->addColumn(
                $columnList
                    ->children()
                    ->arrayNode($column),
                $config
            );
        }
    }

    /**
     * Add interface configure tree to root tree.
     *
     * @param string $interface     Interface name.
     * @param array  $configuration Default configuration.
     *
     * @return $this
     */
    private function configure(string $interface, array $configuration): self
    {
        $node = $this->root
            ->children()
            ->arrayNode($interface);

        $this->addEntity(
            $node,
            $configuration
        );

        return $this;
    }

}