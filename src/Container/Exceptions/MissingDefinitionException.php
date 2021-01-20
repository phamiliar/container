<?php

declare(strict_types=1);

namespace Phamiliar\Container\Exceptions;

use InvalidArgumentException;

/**
 * Will be thrown if service definition will be missed
 */
class MissingDefinitionException extends InvalidArgumentException implements ContainerException
{
}
