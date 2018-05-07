<?php

namespace Tests\ClassTest\TestClasses;

use ClassTest\ClassTest\ClassTestCase;

/**
 * Class SomeClassTestCase
 */
class SomeClassTestCase extends ClassTestCase
{
    protected function getTestedClassName()
    {
        // Override in mock
        return '';
    }

    protected function getTestedClassConstructorParameters()
    {
        // Override in mock
        return [];
    }

    public function getTestedClass()
    {
        return parent::getTestedClass();
    }
}
