<?php

declare(strict_types=1);

namespace Tests\ClassTest\TestClasses;

/**
 * Class SomeClass
 */
class SomeClass
{
    public $interface;
    public $sameInterface;
    public $array;
    public $someString;
    public $someStringClass;

    public function __construct(
        SomeInterface $interface,
        SomeInterface $sameInterface,
        array $array,
        string $someString,
        string $someStringClass
    ) {
        $this->interface = $interface;
        $this->sameInterface = $sameInterface;
        $this->array = $array;
        $this->someString = $someString;
        $this->someStringClass = $someStringClass;
    }
}
