<?php
/*
 * This file is part of the NeimheadhSolid Bundle.
 *
 * (c) 2023 - present  Mathieu Wambre <contact@neimheadh.fr>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Neimheadh\SolidBundle\Doctrine\Entity\Date;

use DateTimeInterface;
use Neimheadh\SolidBundle\Model\Lifecycle\CreatedTrait;

/**
 * Entity with creation date trait.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
trait CreatedEntityTrait
{

    use CreatedTrait;

    /**
     * Creation date.
     *
     * @var DateTimeInterface|null
     */
    protected ?DateTimeInterface $creationDate = null;

}
