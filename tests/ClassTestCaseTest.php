<?php

declare(strict_types=1);

namespace Tests\ClassTest;

use ClassTest\ClassTest\ClassTestCase;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ProphecySubjectInterface;
use Tests\ClassTest\TestClasses\SomeClass;
use Tests\ClassTest\TestClasses\SomeClassTestCase;
use Tests\ClassTest\TestClasses\SomeInterface;

/**
 * Class ClassTestCaseTest
 */
class ClassTestCaseTest extends TestCase
{
    /**
     * @return void
     * @throws \ReflectionException
     */
    public function testTestedClassInstantiation(): void
    {
        $mockClassTestCase = $this->getMockBuilder(SomeClassTestCase::class)
            ->disableOriginalConstructor()
            ->setMethods(['getTestedClassName', 'getTestedClassConstructorParameters'])
            ->getMock();

        $mockClassTestCase->method('getTestedClassName')->willReturn(SomeClass::class);
        $mockClassTestCase->method('getTestedClassConstructorParameters')->willReturn([
            SomeInterface::class,
            'sameInterface' => SomeInterface::class,
            ['someArray', 'withValues'],
            'a string',
            SomeInterface::class => ClassTestCase::STRING_PARAMETER
        ]);

        /** @var SomeClassTestCase $mockClassTestCase */
        $mockClassTestCase->setUp();

        /** @var SomeClass $testedClass */
        $testedClass = $mockClassTestCase->getTestedClass();

        $this->assertInstanceOf(SomeInterface::class, $testedClass->interface);
        $this->assertInstanceOf(ProphecySubjectInterface::class, $testedClass->interface);
        $this->assertInstanceOf(SomeInterface::class, $testedClass->sameInterface);
        $this->assertInstanceOf(ProphecySubjectInterface::class, $testedClass->sameInterface);
        $this->assertEquals($testedClass->interface, $testedClass->sameInterface);
        $this->assertEquals(['someArray', 'withValues'], $testedClass->array);
        $this->assertEquals('a string', $testedClass->someString);
        $this->assertEquals(SomeInterface::class, $testedClass->someStringClass);
    }
}
