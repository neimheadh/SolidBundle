<?php
/*
 * This file is part of the NeimheadhSolid Bundle.
 *
 * (c) 2023 - present  Mathieu Wambre <contact@neimheadh.fr>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Neimheadh\SolidBundle\Doctrine\Entity\Generic;

use Neimheadh\SolidBundle\Model\Generic\NamedInterface;

/**
 * Entity with name attribute.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
interface NamedEntityInterface extends NamedInterface
{

    /**
     * Bundle configuration name.
     */
    public const CONFIGURATION_NAME = 'named';
}
