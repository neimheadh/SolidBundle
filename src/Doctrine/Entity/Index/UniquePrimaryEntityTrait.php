<?php
/*
 * This file is part of the NeimheadhSolid Bundle.
 *
 * (c) 2023 - present  Mathieu Wambre <contact@neimheadh.fr>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Neimheadh\SolidBundle\Doctrine\Entity\Index;

/**
 * Entity with a single integer primary key trait.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
trait UniquePrimaryEntityTrait
{

    /**
     * Entity id.
     *
     * @var int|null
     */
    protected ?int $id = null;

    /**
     * {@inheritDoc}
     */
    public function getId(): ?int
    {
        return $this->id;
    }

}
