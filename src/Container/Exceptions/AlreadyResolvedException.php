<?php

declare(strict_types=1);

namespace Phamiliar\Container\Exceptions;

use OutOfRangeException;

/**
 * Will be thrown in replacement attempt of already resolved shared service
 */
class AlreadyResolvedException extends OutOfRangeException implements ContainerException
{
}
