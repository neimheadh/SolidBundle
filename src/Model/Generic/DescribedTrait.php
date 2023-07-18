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
 * Described object trait.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
trait DescribedTrait
{
    /**
     * Object description.
     *
     * @var string|null
     */
    protected ?string $description = null;

    /**
     * {@inheritDoc}
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * {@inheritDoc}
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }
}
