<?php

declare(strict_types=1);

namespace Phamiliar\Container;

use Closure;
use Phamiliar\Container\Exceptions\AlreadyResolvedException;
use Phamiliar\Container\Exceptions\MissingDefinitionException;
use Phamiliar\Container\Exceptions\ResolveFailedException;
use Phamiliar\Container\Exceptions\ServiceNotFoundException;

/**
 * {@inheritDoc}
 *
 * First created instance will be set as default
 */
class Container implements ContainerInterface
{
    /**
     * Default container
     *
     * @var ContainerInterface|null
     */
    protected static $default;

    /**
     * List of definitions
     *
     * @var mixed[]
     */
    protected $definitions = [];

    /**
     * List of resolved shared instances
     *
     * @var mixed[]
     */
    protected $instances = [];

    /**
     * {@inheritDoc}
     */
    public static function setDefault(ContainerInterface $container): void
    {
        static::$default = $container;
    }

    /**
     * {@inheritDoc}
     */
    public static function resetDefault(): void
    {
        static::$default = null;
    }

    /**
     * {@inheritDoc}
     */
    public static function getDefault(): ?ContainerInterface
    {
        if (!static::$default) {
            static::$default = new static();
        }

        return static::$default;
    }

    /**
     * Sets first created container as default
     *
     * @see ContainerInterface::getDefault()
     *
     * @constructor
     */
    public function __construct()
    {
        if (!static::$default) {
            static::setDefault($this);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function set(
        string $name,
        $definition = null,
        bool $isShared = false
    ): ContainerInterface {
        if (
            !$definition
            && class_exists($name)
        ) {
            $definition = $name;
        }

        if (!$definition) {
            throw new MissingDefinitionException(sprintf(
                'Definition for service "%s" is missing',
                $name,
            ));
        }

        if (array_key_exists($name, $this->instances)) {
            throw new AlreadyResolvedException(sprintf(
                'Shared service "%s" already registered and resolved',
                $name,
            ));
        }

        $this->definitions[$name] = compact('definition', 'isShared');

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setShared(string $name, $definition = null): ContainerInterface
    {
        return $this->set($name, $definition, true);
    }

    /**
     * {@inheritDoc}
     */
    public function remove(string $name): ContainerInterface
    {
        unset(
            $this->definitions[$name],
            $this->instances[$name],
        );

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->definitions);
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $name, ...$parameters)
    {
        return array_key_exists($name, $this->instances)
            ? $this->instances[$name]
            : $this->resolve($name, $parameters);
    }

    /**
     * {@inheritDoc}
     */
    public function isShared(string $name): bool
    {
        if (!$this->has($name)) {
            throw new ServiceNotFoundException(sprintf(
                'Service "%s" is not found',
                $name,
            ));
        }

        return !empty($this->definitions[$name]['isShared']);
    }

    /**
     * {@inheritDoc}
     */
    public function getServices(): array
    {
        return $this->definitions;
    }

    /**
     * Resolves new service instance
     *
     * @param string $name Service name
     * @param mixed[] $parameters Service parameters
     * @throws ServiceNotFoundException If service is not found
     * @throws ResolveFailedException If service can not be resolved
     * @return mixed
     */
    protected function resolve(string $name, array $parameters = [])
    {
        if (!$this->has($name)) {
            throw new ServiceNotFoundException(sprintf(
                'Service "%s" is not found',
                $name,
            ));
        }

        $definition = $this->definitions[$name]['definition'];
        $instance = $this->build($definition, $parameters);

        if (!$instance) {
            throw new ResolveFailedException(sprintf(
                'Service "%s" can not be resolved',
                $name,
            ));
        }

        // Pass container itself if instance implements ContainerAwareInterface
        if ($instance instanceof ContainerAwareInterface) {
            $instance->setContainer($this);
        }

        // Store shared service
        if ($this->isShared($name)) {
            $this->instances[$name] = $instance;
        }

        return $instance;
    }

    /**
     * Build service instance from definition
     *
     * @param mixed $definition Definition
     * @param mixed[] $parameters Service parameters
     * @return mixed
     */
    protected function build($definition, array $parameters)
    {
        if (
            is_string($definition)
            && class_exists($definition)
        ) {
            $instance = new $definition(...$parameters);
        } elseif ($definition instanceof Closure) {
            $instance = $definition($this, ...$parameters);
        } elseif (is_callable($definition)) {
            $instance = $definition(...$parameters);
        } elseif (is_object($definition)) {
            $instance = $definition;
        } else {
            $instance = null;
        }

        return $instance;
    }
}
