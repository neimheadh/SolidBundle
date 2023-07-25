<?php
/*
 * This file is part of the NeimheadhSolid Bundle.
 *
 * (c) 2023 - present  Mathieu Wambre <contact@neimheadh.fr>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Neimheadh\SolidBundle\Doctrine\EventListener\Join;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Neimheadh\SolidBundle\Doctrine\Entity\Join\DefaultJointEntityInterface;
use Neimheadh\SolidBundle\Doctrine\Repository\Join\DefaultJointEntityRepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Default joint & mapped entities lifecycle listener.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class DefaultJointEntityListener
{

    /**
     * Handle doctrine pre-persist or pre-update events.
     *
     * @param PrePersistEventArgs|PreUpdateEventArgs $args Event arguments.
     *
     * @return void
     */
    public function __invoke(PrePersistEventArgs|PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        $manager = $args->getObjectManager();

        if ($manager instanceof EntityManagerInterface) {
            $entity instanceof DefaultJointEntityInterface && $this->switchDefault(
                $manager,
                $entity,
            );
            $this->joinDefault(
                $manager,
                $entity,
            );
        }
    }

    /**
     * Join default entities connected to the persisted/updated entity.
     *
     * @param EntityManagerInterface $manager Object manager.
     * @param object                 $entity  Changed entity.
     *
     * @return void
     */
    private function joinDefault(
        EntityManagerInterface $manager,
        object $entity,
    ): void {
        $accessor = PropertyAccess::createPropertyAccessor();
        $metadata = $manager->getClassMetadata(get_class($entity));

        foreach ($metadata->getAssociationMappings() as $property => $map) {
            if (($target = $map['targetEntity'] ?? null)
                && in_array(
                    DefaultJointEntityInterface::class,
                    class_implements($target)
                )
                && $accessor->getValue($entity, $property) === null
                && ($map['isOwningSide'] ?? null)
            ) {
                // We find the default entity in database.
                $default = current(
                    $manager->createQueryBuilder()
                        ->select(['e'])
                        ->from($target, 'e')
                        ->where('e.isDefault = :default')
                        ->setParameter('default', true)
                        ->getQuery()
                        ->execute()
                ) ?: null;

                // We check the default entity is not in unit of work.
                /** @var DefaultJointEntityInterface $item */
                foreach (
                    $this->getUnitOfWorkObjects(
                        $manager,
                        $target
                    ) as $item
                ) {
                    if ($item->isDefault()) {
                        $default = $item;
                        break;
                    }
                }

                // We set the property to the default entity.
                PropertyAccess::createPropertyAccessor()
                    ->setValue($entity, $property, $default);
            }
        }
    }

    /**
     * Get the list of object in manager unit of work having the given class.
     *
     * Return items in insertion, then updates and finally update collections.
     * If an object is in several unit of work list, it'll be returned several
     * times.
     *
     * @param EntityManagerInterface $manager The entity manager.
     * @param string|object          $class   The object class.
     *
     * @return object[]
     */
    private function getUnitOfWorkObjects(
        EntityManagerInterface $manager,
        string|object $class,
    ): array {
        $items = [];

        $class = is_string($class)
            ? $class
            : get_class($class);

        foreach (
            [
                [$manager->getUnitOfWork()->getScheduledEntityInsertions()],
                [$manager->getUnitOfWork()->getScheduledCollectionUpdates()],
                $manager->getUnitOfWork()->getScheduledCollectionUpdates(),
            ] as $collectionList
        ) {
            foreach ($collectionList as $collection) {
                foreach ($collection as $item) {
                    if ($item instanceof $class) {
                        $items[] = $item;
                    }
                }
            }
        }

        return $items;
    }

    /**
     * Set null to currently true default switch.
     *
     * @param EntityManagerInterface      $manager Object manager.
     * @param DefaultJointEntityInterface $entity  Changed entity.
     *
     * @return void
     */
    private function switchDefault(
        EntityManagerInterface $manager,
        DefaultJointEntityInterface $entity,
    ): void {
        // Set isDefault value to null in database.
        $manager->createQueryBuilder()
            ->update($entity::class, 'e')
            ->set('e.isDefault', ':default')
            ->where('e.isDefault IS NOT NULL')
            ->getQuery()->execute(['default' => null]);

        // Set isDefault value to null in unit of work.
        $entities = $this->getUnitOfWorkObjects($manager, $entity);
        array_walk(
            $entities,
            fn(DefaultJointEntityInterface $entity) => $entity->setIsDefault(
                null,
            ),
        );
    }

}