<?php

declare(strict_types=1);

namespace ClassTest\Tests\TestClasses;

use ClassTest\ClassTestCase;

/**
 * Class SomeClassTestCase
 *
 * @template T of object
 * @extends ClassTestCase<T>
 */
class SomeClassTestCase extends ClassTestCase
{
    protected function getTestedClassName(): string
    {
        // Override in mock
        return '';
    }

    /**
     * @return array<mixed>
     */
    protected function getTestedClassConstructorParameters(): array
    {
        // Override in mock
        return [];
    }
}
