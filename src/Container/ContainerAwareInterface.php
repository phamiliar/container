<?php

declare(strict_types=1);

namespace Phamiliar\Container;

/**
 * This interface should be implemented at classes that uses internally
 * service container that creates them
 */
interface ContainerAwareInterface
{
    /**
     * Sets container
     *
     * @param ContainerInterface $container Container
     * @return $this
     */
    public function setContainer(ContainerInterface $container): ContainerAwareInterface;

    /**
     * Gets container
     *
     * @return ContainerInterface|null
     */
    public function getContainer(): ?ContainerInterface;
}
