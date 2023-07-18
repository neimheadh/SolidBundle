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
 * Object with description.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
interface DescribedInterface
{
    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * Set description.
     *
     * @param string|null $description Object description.
     *
     * @return $this
     */
    public function setDescription(?string $description): self;
}
