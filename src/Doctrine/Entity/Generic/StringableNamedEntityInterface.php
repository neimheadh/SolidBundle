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

use Neimheadh\SolidBundle\Model\Generic\StringableNamedInterface;

/**
 * Stringable named entity.
 */
interface StringableNamedEntityInterface extends StringableNamedInterface,
                                                 NamedEntityInterface
{

}