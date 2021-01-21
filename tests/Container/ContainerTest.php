<?php

declare(strict_types=1);

namespace Tests\Phamiliar\Container;

use Phamiliar\Container\Container;
use Phamiliar\Container\ContainerInterface;
use Phamiliar\Container\Exceptions\AlreadyResolvedException;
use Phamiliar\Container\Exceptions\MissingDefinitionException;
use Phamiliar\Container\Exceptions\ResolveFailedException;
use Phamiliar\Container\Exceptions\ServiceNotFoundException;
use PHPUnit\Framework\TestCase;
use Tests\Phamiliar\Container\ContainerTest\SampleService;

/**
 * @covers \Phamiliar\Container\Container
 * @coversDefaultClass \Phamiliar\Container\Container
 */
class ContainerTest extends TestCase
{
    /**
     * Test of default container setter, getter and reset
     *
     * @return void
     *
     * @covers ::getDefault
     * @covers ::resetDefault
     * @covers ::setDefault
     */
    public function testDefault(): void
    {
        $containerDefault1 = Container::getDefault();
        $container1 = new Container();
        $container2 = new Container();

        static::assertSame($containerDefault1, Container::getDefault());

        Container::setDefault($container2);

        static::assertSame($container2, Container::getDefault());

        Container::setDefault($container1);

        static::assertSame($container1, Container::getDefault());

        Container::resetDefault();

        $containerDefault2 = Container::getDefault();

        static::assertNotSame($container1, Container::getDefault());
        static::assertNotSame($container2, Container::getDefault());
        static::assertNotSame($containerDefault1, Container::getDefault());
        static::assertSame($containerDefault2, Container::getDefault());
    }

    /**
     * Test of constructor
     *
     * @return void
     *
     * @depends testDefault
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        Container::resetDefault();

        $container1 = new Container();
        $container2 = new Container();

        static::assertSame($container1, Container::getDefault());
        static::assertNotSame($container2, Container::getDefault());
    }

    /**
     * Test of service setter and existance checker
     *
     * @return void
     *
     * @covers ::has
     * @covers ::set
     */
    public function testSetHas(): void
    {
        $container = new Container();

        $name = uniqid('service_', true);

        static::assertFalse($container->has($name));

        $container->set($name, SampleService::class);

        static::assertTrue($container->has($name));
    }

    /**
     * Test of shared state setter and getter
     *
     * @return void
     *
     * @covers ::isShared
     * @covers ::set
     * @covers ::setShared
     */
    public function testSetShared(): void
    {
        $container = new Container();

        $name = uniqid('service_', true);
        $sharedName1 = uniqid('shared_', true);
        $sharedName2 = uniqid('shared_', true);

        $container->set($name, SampleService::class);
        $container->set($sharedName1, SampleService::class, true);
        $container->setShared($sharedName2, SampleService::class);

        static::assertFalse($container->isShared($name));
        static::assertTrue($container->isShared($sharedName1));
        static::assertTrue($container->isShared($sharedName2));
    }

    /**
     * Test of service setter with missing definition
     *
     * @return void
     *
     * @covers ::set
     */
    public function testSetMissingDefinition(): void
    {
        $name = uniqid('service_', true);

        $this->expectException(MissingDefinitionException::class);

        (new Container())->set($name, '');
    }

    /**
     * Test of service setter with already resolved shared definition
     *
     * @return void
     *
     * @depends testGetShared
     * @covers ::set
     */
    public function testSetResolvedShared(): void
    {
        $container = new Container();

        $name = uniqid('service_', true);

        $container->setShared($name, SampleService::class);

        $container->get($name);

        $this->expectException(AlreadyResolvedException::class);

        $container->set($name, SampleService::class);
    }

    /**
     * Test of service resolving by class name
     *
     * @return void
     *
     * @covers ::get
     */
    public function testGetResolveClassName(): void
    {
        $container = new Container();

        $name = uniqid('service_', true);
        $definition = SampleService::class;
        $expected = SampleService::class;

        $container->set($name, $definition);

        static::assertInstanceOf($expected, $container->get($name));
    }

    /**
     * Test of service resolving by class name with passed parameters
     *
     * @return void
     *
     * @covers ::get
     */
    public function testGetResolveClassNameWithParameters(): void
    {
        $container = new Container();

        $name = uniqid('service_', true);
        $definition = SampleService::class;
        $parameter1 = uniqid('parameter_', true);
        $parameter2 = uniqid('parameter_', true);

        $container->set($name, $definition);

        static::assertSame(
            $parameter1,
            $container->get($name, [$parameter1])->property
        );
        static::assertSame(
            $parameter2,
            $container->get($name, [$parameter2])->property
        );
    }

    /**
     * Test of service resolving from instance
     *
     * @return void
     *
     * @covers ::get
     */
    public function testGetResolveObject(): void
    {
        $container = new Container();

        $name = uniqid('service_', true);
        $definition = new SampleService();

        $container->set($name, $definition);

        static::assertSame($definition, $container->get($name));
    }

    /**
     * Test of service resolving from closure
     *
     * @return void
     *
     * @covers ::get
     */
    public function testGetResolveClosure(): void
    {
        $container = new Container();

        $name = uniqid('service_', true);
        $definition = static function (): SampleService {
            return new SampleService();
        };
        $expected = SampleService::class;

        $container->set($name, $definition);

        static::assertInstanceOf($expected, $container->get($name));
    }

    /**
     * Test of service resolving from closure with passed parameters
     *
     * @return void
     *
     * @covers ::get
     */
    public function testGetResolveClosureWithParameters(): void
    {
        $container = new Container();

        $name = uniqid('service_', true);
        $definition = static function (
            ContainerInterface $serviceContainer,
            ...$parameters
        ) use ($container): SampleService {
            static::assertSame($container, $serviceContainer);

            return new SampleService(...$parameters);
        };
        $parameter1 = uniqid('parameter_', true);
        $parameter2 = uniqid('parameter_', true);

        $container->set($name, $definition);

        static::assertSame(
            $parameter1,
            $container->get($name, [$parameter1])->property
        );
        static::assertSame(
            $parameter2,
            $container->get($name, [$parameter2])->property
        );
    }

    /**
     * Test of service resolving from callable
     *
     * @return void
     *
     * @covers ::get
     */
    public function testGetResolveCallable(): void
    {
        $container = new Container();

        $name = uniqid('service_', true);
        $definition = [SampleService::class, 'create'];
        $expected = SampleService::class;

        $container->set($name, $definition);

        static::assertInstanceOf($expected, $container->get($name));
    }

    /**
     * Test of service resolving from callable with passed parameters
     *
     * @return void
     *
     * @covers ::get
     */
    public function testGetResolveCallableWithParameters(): void
    {
        $container = new Container();

        $name = uniqid('service_', true);
        $definition = [SampleService::class, 'create'];
        $parameter1 = uniqid('parameter_', true);
        $parameter2 = uniqid('parameter_', true);

        $container->set($name, $definition);

        static::assertSame(
            $parameter1,
            $container->get($name, [$parameter1])->property
        );
        static::assertSame(
            $parameter2,
            $container->get($name, [$parameter2])->property
        );
    }

    /**
     * Test of service resolving for non shared instances
     *
     * @return void
     *
     * @covers ::get
     */
    public function testGetNonShared(): void
    {
        $container = new Container();

        $name = uniqid('service_', true);

        $container->set($name, SampleService::class);

        static::assertNotSame(
            $container->get($name),
            $container->get($name)
        );
    }

    /**
     * Test of service resolving for shared instances
     *
     * @return void
     *
     * @covers ::get
     */
    public function testGetShared(): void
    {
        $container = new Container();

        $name = uniqid('service_', true);

        $container->setShared($name, SampleService::class);

        static::assertSame(
            $container->get($name),
            $container->get($name)
        );
    }

    /**
     * Test of service resolving for shared instances with parameters
     *
     * @return void
     *
     * @covers ::get
     */
    public function testGetSharedWithParameters(): void
    {
        $container = new Container();

        $name = uniqid('service_', true);
        $parameter1 = uniqid('parameter_', true);
        $parameter2 = uniqid('parameter_', true);

        $container->setShared($name, SampleService::class);

        static::assertSame(
            $parameter1,
            $container->get($name, [$parameter1])->property
        );
        static::assertSame(
            $parameter1,
            $container->get($name, [$parameter2])->property
        );
    }

    /**
     * Test of service resolving with non found service
     *
     * @return void
     *
     * @covers ::get
     */
    public function testGetNotFound(): void
    {
        $container = new Container();

        $name = uniqid('service_', true);

        $this->expectException(ServiceNotFoundException::class);

        $container->get($name);
    }

    /**
     * Test of service resolving with non-resolable definition
     *
     * @return void
     *
     * @covers ::get
     */
    public function testGetFailed(): void
    {
        $container = new Container();

        $name = uniqid('service_', true);

        $container->set($name, $name);

        $this->expectException(ResolveFailedException::class);

        $container->get($name);
    }

    /**
     * Test of service removal
     *
     * @return void
     *
     * @depends testSetHas
     * @covers ::remove
     */
    public function testRemove(): void
    {
        $container = new Container();

        $name = uniqid('service_', true);

        $container->set($name, SampleService::class);

        $container->remove($name);

        static::assertFalse($container->has($name));
    }

    /**
     * Test of shared service removal
     *
     * @return void
     *
     * @depends testGetShared
     * @depends testSetHas
     * @covers ::remove
     */
    public function testRemoveShared(): void
    {
        $container = new Container();

        $service = uniqid('service_', true);

        $container->setShared($service, SampleService::class);

        $definition1 = $container->get($service);

        $container->remove($service);

        $container->setShared($service, SampleService::class);

        $definition2 = $container->get($service);

        static::assertNotSame($definition1, $definition2);
    }

    /**
     * Test of registered services getter
     *
     * @return void
     *
     * @covers ::getServices
     */
    public function testGetServices(): void
    {
        $container = new Container();

        $service1 = uniqid('service_', true);
        $service2 = uniqid('service_', true);
        $definition1 = static function (): SampleService {
            return new SampleService();
        };
        $definition2 = static function (): SampleService {
            return new SampleService();
        };
        $expected1 = [
            $service1 => [
                'definition' => $definition1,
                'isShared' => false,
            ],
        ];
        $expected2 = [
            $service1 => [
                'definition' => $definition1,
                'isShared' => false,
            ],
            $service2 => [
                'definition' => $definition2,
                'isShared' => true,
            ],
        ];

        static::assertEmpty($container->getServices());

        $container->set($service1, $definition1);

        static::assertSame($expected1, $container->getServices());

        $container->set($service2, $definition2, true);

        static::assertSame($expected2, $container->getServices());
    }
}
