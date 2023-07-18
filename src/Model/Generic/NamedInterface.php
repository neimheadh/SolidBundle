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
 * Named object.
 *
 * @author <contact@neimheadh.fr>
 */
interface NamedInterface
{
    /**
     * Get name.
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Set name.
     *
     * @param string|null $name Object name.
     *
     * @return $this
     */
    public function setName(?string $name): self;
}
