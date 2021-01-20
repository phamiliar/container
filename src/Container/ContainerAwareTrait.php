<?php

declare(strict_types=1);

namespace Phamiliar\Container;

/**
 * This trait implements ContainerAwareInterface
 */
trait ContainerAwareTrait
{
    /**
     * Service container
     *
     * @var ContainerInterface|null
     */
    protected $container;

    /**
     * Sets container
     *
     * @param ContainerInterface $container Container
     * @return $this
     */
    public function setContainer(ContainerInterface $container): ContainerAwareInterface
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Gets container
     *
     * @return ContainerInterface|null
     */
    public function getContainer(): ?ContainerInterface
    {
        if (!$this->container) {
            $this->container = Container::getDefault();
        }

        return $this->container;
    }
}
