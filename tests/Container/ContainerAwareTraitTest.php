<?php

declare(strict_types=1);

namespace Tests\Phamiliar\Container;

use Phamiliar\Container\Container;
use PHPUnit\Framework\TestCase;
use Tests\Phamiliar\Container\ContainerAwareTraitTest\ContainerAwareClass;

/**
 * @covers \Phamiliar\Container\ContainerAwareTrait
 * @coversDefaultClass \Phamiliar\Container\ContainerAwareTrait
 */
class ContainerAwareTraitTest extends TestCase
{
    /**
     * Test of container setter and getter
     *
     * @return void
     *
     * @covers ::getContainer
     * @covers ::setContainer
     */
    public function testSetGetContainer(): void
    {
        $container1 = new Container();
        $container2 = new Container();
        Container::resetDefault();

        $containerAware = new ContainerAwareClass();

        static::assertNull($containerAware->getContainer());

        Container::setDefault($container1);

        static::assertSame($container1, $containerAware->getContainer());

        $containerAware->setContainer($container2);

        static::assertSame($container2, $containerAware->getContainer());
    }
}
