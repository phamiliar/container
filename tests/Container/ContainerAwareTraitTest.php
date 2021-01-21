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
        $containerDefault = Container::getDefault();
        $container = new Container();

        $containerAware = new ContainerAwareClass();

        static::assertSame($containerDefault, $containerAware->getContainer());

        $containerAware->setContainer($container);

        static::assertSame($container, $containerAware->getContainer());
    }
}
