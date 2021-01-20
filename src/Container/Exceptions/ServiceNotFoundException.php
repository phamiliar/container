<?php

declare(strict_types=1);

namespace Phamiliar\Container\Exceptions;

use OutOfRangeException;

/**
 * Will be thrown if requested service not found
 */
class ServiceNotFoundException extends OutOfRangeException implements ContainerException
{
}
