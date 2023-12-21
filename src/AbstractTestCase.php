<?php

declare(strict_types=1);

namespace ClassTest;

use ClassTest\Exception\ProphesizedObjectNotFoundException;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class AbstractTestCase
 *
 * @author Clement Malet
 *
 * @template T of object
 */
abstract class AbstractTestCase extends TestCase
{
    use ProphecyTrait {
        prophesize as parentProphesize;
    }

    public const DEFAULT_PROPHECY = 0;
    public const DUMMY_PROPHECY = 1;

    /**
     * Every ObjectProphecy is considered to be a mock here
     * The 'Dummy' keyword is only used to instantiate what we will then consider a mock
     *
     * @var ObjectProphecy<T>[] $mocks
     */
    private array $mocks = [];

    /**
     * @param class-string<T> $mockName
     * @param bool $createIfNotFound
     * @return ObjectProphecy<T>
     * @throws \ReflectionException
     */
    protected function getProphecy(string $mockName, bool $createIfNotFound = false): ObjectProphecy
    {
        if (isset($this->mocks[$mockName])) {
            return $this->mocks[$mockName];
        }

        if ($createIfNotFound) {
            $this->addNewStub($mockName);
        } else {
            throw new ProphesizedObjectNotFoundException();
        }

        return $this->mocks[$mockName];
    }

    /**
     * @param class-string<T> $prophecyName
     * @param string $methodName
     * @param array<mixed>|null $arguments
     * @return MethodProphecy
     * @throws \ReflectionException
     */
    protected function getProphecyMethod(
        string $prophecyName,
        string $methodName,
        array|null $arguments = null
    ): MethodProphecy {
        return TestTools::getProphecyMethod($this->getProphecy($prophecyName), $methodName, $arguments);
    }

    /**
     * Extension of the prophesize() method that provides a way to initiate a dummy with "void" methods,
     * allowing all methods to take any arguments and to return null.
     *
     * Methods behavior can still be changed later on, though.
     *
     * @param class-string<T> $class
     * @param int $prophecyDummyType
     * @return ObjectProphecy<T>
     * @throws \ReflectionException
     */
    protected function prophesize(string $class, int $prophecyDummyType = self::DEFAULT_PROPHECY): ObjectProphecy
    {
        return $prophecyDummyType === self::DUMMY_PROPHECY
            ? $this->parentProphesize($class)
            : $this->prophesizeDummy($class);
    }

    /**
     * Initiate a dummy with "void" methods, allowing all methods to take any arguments and to return null.
     *
     * @param class-string<T> $class
     * @return ObjectProphecy<T>
     * @throws \ReflectionException
     * @see prophesize
     */
    protected function prophesizeDummy(string $class): ObjectProphecy
    {
        $prophecy = $this->parentProphesize($class);
        TestTools::setDummyProphecy($prophecy, $class);

        return $prophecy;
    }

    /**
     * @return ObjectProphecy<T>[]
     */
    protected function getMocks(): array
    {
        return $this->mocks;
    }

    /**
     * @param ObjectProphecy<T>[] $mocks
     */
    protected function setMocks(array $mocks): void
    {
        foreach ($mocks as $name => $mock) {
            $this->addMock($mock, $name);
        }
    }

    /**
     * Adds a mock object to the mocks collection
     *
     * @param ObjectProphecy<T> $mock
     * @param string         $keyName
     */
    protected function addMock(ObjectProphecy $mock, string $keyName): void
    {
        $this->mocks[$keyName] = $mock;
    }

    /**
     * Adds a mock from a class name, then returns it
     *
     * @param class-string<T> $mockClassName
     * @param string|null $mockName
     * @return ObjectProphecy<T>
     * @throws \ReflectionException
     */
    protected function addNewStub(string $mockClassName, string|null $mockName = null): ObjectProphecy
    {
        TestTools::assertIsProphesizable($mockClassName);
        $mock = $this->prophesize($mockClassName);

        $this->addMock($mock, $mockName === null ? $mockClassName : $mockName);

        return $mock;
    }

    /**
     * Adds a dummy from a class name
     *
     * @param class-string<T> $dummyClassName
     * @param string|null $dummyName
     * @return ObjectProphecy<T>
     * @throws \ReflectionException
     */
    protected function addNewDummy(string $dummyClassName, string|null $dummyName = null): ObjectProphecy
    {
        TestTools::assertIsProphesizable($dummyClassName);
        $dummy = $this->prophesizeDummy($dummyClassName);

        $this->addMock($dummy, $dummyName === null ? $dummyClassName : $dummyName);

        return $dummy;
    }
}
