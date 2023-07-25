<?php
/*
 * This file is part of the NeimheadhSolid Bundle.
 *
 * (c) 2023 - present  Mathieu Wambre <contact@neimheadh.fr>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Neimheadh\SolidBundle\Doctrine\Entity\Join;

/**
 * Entity that can be default joint to mapped entities.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
interface DefaultJointEntityInterface
{

    /**
     * Is the entity default joint to mapped entities.
     *
     * @return bool
     */
    public function isDefault(): bool;

    /**
     * Change the default joint status.
     *
     * The change of the default status is listened so the last entity marked
     * has default override the default status of other rows.
     *
     * @param bool|null $default The default joint status.
     *
     * @return $this
     */
    public function setIsDefault(?bool $default = true): self;
}