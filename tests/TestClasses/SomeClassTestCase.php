<?php

declare(strict_types=1);

namespace Tests\ClassTest\TestClasses;

use ClassTest\ClassTest\ClassTestCase;

/**
 * Class SomeClassTestCase
 */
class SomeClassTestCase extends ClassTestCase
{
    protected function getTestedClassName(): string
    {
        // Override in mock
        return '';
    }

    protected function getTestedClassConstructorParameters(): array
    {
        // Override in mock
        return [];
    }

    public function getTestedClass()
    {
        return parent::getTestedClass();
    }
}
