<?php

declare(strict_types=1);

namespace ClassTest\Tests\TestClasses;

/**
 * Class SomeClass
 */
class SomeClass
{
    /**
     * @param array<mixed> $array
     */
    public function __construct(
        public readonly SomeInterface $interface,
        public readonly SomeInterface $sameInterface,
        public readonly array $array,
        public readonly string $someString,
        public readonly string $someStringClass
    ) {}
}
