<?php

declare(strict_types=1);

namespace Tests\Phamiliar\Container\ContainerAwareTraitTest;

use Phamiliar\Container\ContainerAwareInterface;
use Phamiliar\Container\ContainerAwareTrait;

/**
 * Test class for ContainerAwareTrait trait
 */
class ContainerAwareClass implements ContainerAwareInterface
{
    use ContainerAwareTrait;
}
