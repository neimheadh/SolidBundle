<?php
/*
 * This file is part of the NeimheadhSolid Bundle.
 *
 * (c) 2023 - present  Mathieu Wambre <contact@neimheadh.fr>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Neimheadh\SolidBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SolidBundle\Doctrine\Entity\Index\UniquePrimaryEntityInterface;
use Neimheadh\SolidBundle\Doctrine\Entity\Index\UniquePrimaryEntityTrait;
use Neimheadh\SolidBundle\Doctrine\Entity\Join\DefaultJointEntityInterface;
use Neimheadh\SolidBundle\Doctrine\Entity\Join\DefaultJointEntityTrait;

/**
 * Entity overriding default isDefault.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
#[ORM\Entity]
#[ORM\Table(name: 'default_override')]
class DefaultOverrideEntity implements UniquePrimaryEntityInterface,
                                       DefaultJointEntityInterface
{

    use UniquePrimaryEntityTrait;
    use DefaultJointEntityTrait;

    /**
     * {@inheritDoc}
     */
    #[ORM\Column(
        type: 'boolean',
        unique: false,
        nullable: true,
        options: ['default' => null],
    )]
    protected ?bool $isDefault = null;
}