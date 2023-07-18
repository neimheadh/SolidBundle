<?php
/*
 * This file is part of the NeimheadhSolid Bundle.
 *
 * (c) 2023 - present  Mathieu Wambre <contact@neimheadh.fr>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Neimheadh\SolidBundle\Tests\Test;

use Neimheadh\SolidBundle\Tests\Kernel;
use Neimheadh\SolidBundle\Tests\NoDoctrineKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Throwable;

/**
 * Bundle implementation test.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
class ImplementationTest extends WebTestCase
{

    /**
     * Test bundle implementation on a kernel without doctrine does not throw
     * any error.
     *
     * @return void
     */
    public function testNoDoctrineImplementation(): void
    {
        $kernel = static::bootKernel();
        $test = new NoDoctrineKernel($kernel->getEnvironment(), $kernel->isDebug());

        $e = null;
        try {
            $test->boot();
        } catch (Throwable $e) {}

        $this->assertNull($e);
    }

}