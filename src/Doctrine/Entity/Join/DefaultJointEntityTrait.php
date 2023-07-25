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
 * Entity that can be default joint to mapped entities trait.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
trait DefaultJointEntityTrait
{

    /**
     * Default joint status.
     *
     * @var bool
     */
    protected ?bool $isDefault = null;

    /**
     * {@inheritDoc}
     */
    public function isDefault(): bool
    {
        return (bool)$this->isDefault;
    }

    /**
     * {@inheritDoc}
     */
    public function setIsDefault(?bool $default = true): self
    {
        $this->isDefault = $default ? true : null;

        return $this;
    }
}