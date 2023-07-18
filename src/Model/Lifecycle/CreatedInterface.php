<?php
/*
 * This file is part of the NeimheadhSolid Bundle.
 *
 * (c) 2023 - present  Mathieu Wambre <contact@neimheadh.fr>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Neimheadh\SolidBundle\Model\Lifecycle;

use DateTimeInterface;

/**
 * Object with creation date.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
interface CreatedInterface
{
    /**
     * Get creation date.
     *
     * @return DateTimeInterface|null
     */
    public function getCreationDate(): ?DateTimeInterface;

    /**
     * Set creation date.
     *
     * @param DateTimeInterface|null $creationDate Object creation date.
     *
     * @return $this
     */
    public function setCreationDate(?DateTimeInterface $creationDate): self;
}
