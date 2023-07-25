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
use Neimheadh\SolidBundle\Doctrine\Entity\Generic\DescribedEntityInterface;
use Neimheadh\SolidBundle\Doctrine\Entity\Generic\DescribedEntityTrait;
use Neimheadh\SolidBundle\Doctrine\Entity\Generic\NamedEntityInterface;
use Neimheadh\SolidBundle\Doctrine\Entity\Generic\NamedEntityTrait;
use Neimheadh\SolidBundle\Doctrine\Entity\Index\UniquePrimaryEntityInterface;
use Neimheadh\SolidBundle\Doctrine\Entity\Index\UniquePrimaryEntityTrait;
use Neimheadh\SolidBundle\Doctrine\Entity\Date\DatedEntityInterface;
use Neimheadh\SolidBundle\Doctrine\Entity\Date\DatedEntityTrait;

/**
 * Generic entity.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
#[ORM\Entity]
#[ORM\Table(name: 'generic')]
class GenericEntity implements UniquePrimaryEntityInterface,
                               DatedEntityInterface,
                               DescribedEntityInterface,
                               NamedEntityInterface
{

    use UniquePrimaryEntityTrait;
    use DescribedEntityTrait;
    use NamedEntityTrait;
    use DatedEntityTrait;

    /**
     * Default override test.
     *
     * @var DefaultOverrideEntity|null
     */
    #[ORM\ManyToOne(targetEntity: DefaultOverrideEntity::class)]
    private ?DefaultOverrideEntity $defaultOverride = null;

    /**
     * Owner.
     *
     * @var OwnerEntity|null
     */
    #[ORM\ManyToOne(targetEntity: OwnerEntity::class)]
    private ?OwnerEntity $owner = null;

    /**
     * Get default override.
     *
     * @return DefaultOverrideEntity|null
     */
    public function getDefaultOverride(): ?DefaultOverrideEntity
    {
        return $this->defaultOverride;
    }

    /**
     * Get owner.
     *
     * @return OwnerEntity|null
     */
    public function getOwner(): ?OwnerEntity
    {
        return $this->owner;
    }

    /**
     * Set default override.
     *
     * @param DefaultOverrideEntity|null $defaultOverride Default override.
     *
     * @return $this
     */
    public function setDefaultOverride(?DefaultOverrideEntity $defaultOverride
    ): self {
        $this->defaultOverride = $defaultOverride;
        return $this;
    }

    /**
     * Set owner.
     *
     * @param OwnerEntity|null $owner Owner.
     *
     * @return $this
     */
    public function setOwner(?OwnerEntity $owner): self
    {
        $this->owner = $owner;
        return $this;
    }
}