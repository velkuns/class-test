<?php

declare(strict_types=1);

namespace ClassTest\Tests;

use ClassTest\ClassTestCase;
use ClassTest\Tests\TestClasses\SomeClass;
use ClassTest\Tests\TestClasses\SomeClassTestCase;
use ClassTest\Tests\TestClasses\SomeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ProphecySubjectInterface;

/**
 * Class ClassTestCaseTest
 */
class ClassTestCaseTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testTestedClassInstantiation(): void
    {
        /** @var SomeClassTestCase<SomeClass>&MockObject $mockClassTestCase */
        $mockClassTestCase = $this->getMockBuilder(SomeClassTestCase::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getTestedClassName', 'getTestedClassConstructorParameters'])
            ->getMock();

        $mockClassTestCase->method('getTestedClassName')->willReturn(SomeClass::class);
        $mockClassTestCase->method('getTestedClassConstructorParameters')->willReturn([
            SomeInterface::class,
            'sameInterface' => SomeInterface::class,
            ['someArray', 'withValues'],
            'a string',
            SomeInterface::class => ClassTestCase::STRING_PARAMETER
        ]);


        $mockClassTestCase->setUp();

        $testedClass = $mockClassTestCase->getTestedClass();

        $this->assertInstanceOf(SomeInterface::class, $testedClass->interface);
        $this->assertInstanceOf(ProphecySubjectInterface::class, $testedClass->interface);
        $this->assertInstanceOf(SomeInterface::class, $testedClass->sameInterface);
        $this->assertInstanceOf(ProphecySubjectInterface::class, $testedClass->sameInterface);
        $this->assertNotSame($testedClass->interface, $testedClass->sameInterface);
        $this->assertEquals(['someArray', 'withValues'], $testedClass->array);
        $this->assertEquals('a string', $testedClass->someString);
        $this->assertEquals(SomeInterface::class, $testedClass->someStringClass);
    }
}
