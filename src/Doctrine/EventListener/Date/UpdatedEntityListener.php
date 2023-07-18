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

use DateTime;
use Neimheadh\SolidBundle\Doctrine\Entity\Date\UpdatedEntityInterface;

/**
 * Entity with update date lifecycle listener.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
class UpdatedEntityListener
{

    /**
     * Handle doctrine pre-update event.
     *
     * @param object $entity Updated entity.
     *
     * @return void
     * @internal
     */
    public function preUpdate(object $entity): void
    {
        if ($entity instanceof UpdatedEntityInterface) {
            $this->setUpdateDate($entity);
        }
    }

    /**
     * Set entity update date with the current date.
     *
     * @param UpdatedEntityInterface $entity Updated entity.
     *
     * @return void
     */
    protected function setUpdateDate(UpdatedEntityInterface $entity): void
    {
        $now = new DateTime();

        $entity->getUpdateDate() < $now && $entity->setUpdateDate($now);
    }

}
