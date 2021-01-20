<?php

declare(strict_types=1);

namespace Phamiliar\Container\Exceptions;

use UnexpectedValueException;

/**
 * Will be thrown if service can not be resolved
 */
class ResolveFailedException extends UnexpectedValueException implements ContainerException
{
}
