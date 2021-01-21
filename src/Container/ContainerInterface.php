<?php

declare(strict_types=1);

namespace Phamiliar\Container;

use Phamiliar\Container\Exceptions\AlreadyResolvedException;
use Phamiliar\Container\Exceptions\MissingDefinitionException;
use Phamiliar\Container\Exceptions\ResolveFailedException;
use Phamiliar\Container\Exceptions\ServiceNotFoundException;

/**
 * Container is a component that implements Dependency Injection/Service
 * Location of services and container for them.
 *
 * Since Framework is highly decoupled, container is essential to integrate the
 * different components of the framework. The developer can also use this
 * component to inject dependencies and manage global instances of the different
 * classes used in the application.
 *
 * Basically, this component implements the `Inversion of Control` pattern.
 * Applying this, the objects do not receive their dependencies using setters or
 * constructors, but requesting a service dependency injector. This reduces the
 * overall complexity, since there is only one way to get the required
 * dependencies within a component.
 *
 * Additionally, this pattern increases testability in the code, thus making it
 * less prone to errors.
 *
 * <code>
 * $container = new Container();
 *
 * // Set non-shared service via a string definition
 * $container->set('request', Request::class);
 *
 * // Set shared service via a string definition
 * $container->set('request', Request::class, true);
 *
 * // Set shared service via anonymous function
 * $container->set(
 *     'request',
 *     function () {
 *         return new Request();
 *     },
 *     true
 * );
 *
 * // Get service via getter
 * $request = $container->get('request');
 * </code>
 */
interface ContainerInterface
{
    /**
     * Sets default container to be obtained into static methods
     *
     * @param ContainerInterface $container DI container
     * @return void
     */
    public static function setDefault(ContainerInterface $container): void;

    /**
     * Gets default container
     *
     * @return ContainerInterface|null
     */
    public static function getDefault(): ?ContainerInterface;

    /**
     * Resets default container
     *
     * @return void
     */
    public static function resetDefault(): void;

    /**
     * Registers service in container
     *
     * @param string $name Service name
     * @param mixed $definition Service definition
     * @param bool $isShared Shared state
     * @throws MissingDefinitionException If service definition is not provided
     * @throws AlreadyResolvedException If shared service already has registered and resolved
     * @return $this
     */
    public function set(
        string $name,
        $definition,
        bool $isShared = false
    ): ContainerInterface;

    /**
     * Registers "shared" service in container
     *
     * @param string $name Service name
     * @param mixed $definition Service definition
     * @throws MissingDefinitionException If service definition is not provided
     * @throws AlreadyResolvedException If shared service already has registered and resolved
     * @return $this
     */
    public function setShared(string $name, $definition): ContainerInterface;

    /**
     * Removes service from container
     *
     * Also removes resolved "shared" instance
     *
     * @param string $name Service name
     * @return $this
     */
    public function remove(string $name): ContainerInterface;

    /**
     * Checks whether container contains service by name
     *
     * @param string $name Service name
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * Resolves service based on its configuration
     *
     * Returns same instance for "shared" services
     *
     * @param string $name Service name
     * @param mixed[] ...$parameters Service parameters
     * @throws ServiceNotFoundException If service is not found
     * @throws ResolveFailedException If service can not be resolved
     * @return mixed
     */
    public function get(string $name, ...$parameters);

    /**
     * Checks whether service shared
     *
     * @param string $name Service name
     * @throws ServiceNotFoundException If service is not found
     * @return bool
     */
    public function isShared(string $name): bool;

    /**
     * Gets all registered services
     *
     * @return mixed[]
     */
    public function getServices(): array;
}
