<?php

declare(strict_types=1);

namespace Phamiliar\Container\Exceptions;

use InvalidArgumentException;

/**
 * Will be thrown in replacement attempt of already resolved shared service
 */
class AlreadyResolvedException extends InvalidArgumentException implements ContainerException
{
}
