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
 * Object with update date trait.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
trait UpdatedTrait
{
    /**
     * Object update date.
     *
     * @var DateTimeInterface|null
     */
    protected ?DateTimeInterface $updateDate = null;

    /**
     * {@inheritDoc}
     */
    public function getUpdateDate(): ?DateTimeInterface
    {
        return $this->updateDate;
    }

    /**
     * {@inheritDoc}
     */
    public function setUpdateDate(?DateTimeInterface $updateDate): self
    {
        $this->updateDate = $updateDate;
        return $this;
    }

}
