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
 * Object with update date.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
interface UpdatedInterface
{
    /**
     * Get update date.
     *
     * @return DateTimeInterface|null
     */
    public function getUpdateDate(): ?DateTimeInterface;

    /**
     * Set update date.
     *
     * @param DateTimeInterface|null $updateDate Object update date.
     *
     * @return $this
     */
    public function setUpdateDate(?DateTimeInterface $updateDate): self;
}
