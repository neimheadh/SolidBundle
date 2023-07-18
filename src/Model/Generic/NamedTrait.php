<?php
/*
 * This file is part of the NeimheadhSolid Bundle.
 *
 * (c) 2023 - present  Mathieu Wambre <contact@neimheadh.fr>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Neimheadh\SolidBundle\Model\Generic;

/**
 * Named object trait.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
trait NamedTrait
{
    /**
     * Object name.
     *
     * @var string|null
     */
    protected ?string $name = null;

    /**
     * {@inheritDoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
