<?php

declare(strict_types=1);

namespace Tests\Phamiliar\Container\ContainerTest;

/**
 * Sample service for container tests
 */
class SampleService
{
    /**
     * Sample property
     *
     * @var mixed
     */
    public $property;

    /**
     * Sample callable method to test callable definition
     *
     * @param mixed $property Sample property value
     * @return static
     */
    public static function create($property = null): SampleService
    {
        return new static($property);
    }

    /**
     * @constructor
     * @param mixed $property Sample property value
     */
    public function __construct($property = null)
    {
        $this->property = $property;
    }
}
