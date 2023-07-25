<?php
/*
 * This file is part of the NeimheadhSolid Bundle.
 *
 * (c) 2023 - present  Mathieu Wambre <contact@neimheadh.fr>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Neimheadh\SolidBundle\Doctrine\EventListener\Date;

use DateTimeImmutable;
use Neimheadh\SolidBundle\Doctrine\Entity\Date\CreatedEntityInterface;

/**
 * Entity with creation date lifecycle listener.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class CreatedEntityListener
{

    /**
     * Handle doctrine pre-persist event.
     *
     * @param object $entity Persisted entity.
     *
     * @return void
     * @internal
     */
    public function __invoke(object $entity): void
    {
        if ($entity instanceof CreatedEntityInterface) {
            $this->setCreationDate($entity);
        }
    }

    /**
     * Set entity creation date with the current date.
     *
     * @param CreatedEntityInterface $entity Created entity.
     *
     * @return void
     */
    protected function setCreationDate(CreatedEntityInterface $entity): void
    {
        $now = new DateTimeImmutable();

        $entity->getCreationDate() === null && $entity->setCreationDate($now);
    }

}
