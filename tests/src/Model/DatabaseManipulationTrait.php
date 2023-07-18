<?php
/*
 * This file is part of the NeimheadhSolid Bundle.
 *
 * (c) 2023 - present  Mathieu Wambre <contact@neimheadh.fr>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Neimheadh\SolidBundle\Tests\Model;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Tools\ToolsException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\TestContainer;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * Function helping to manipulate database.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
trait DatabaseManipulationTrait
{

    /**
     * Re-initialize database.
     *
     * @return void
     * @throws Exception
     */
    protected function reinitializeDatabase(): void
    {
        exec(
            sprintf(
                'php %s/bin/reset-database > /dev/null',
                dirname(__DIR__, 3),
            )
        );
    }

}