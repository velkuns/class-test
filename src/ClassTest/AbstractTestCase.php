<?php

declare(strict_types=1);

namespace ClassTest\ClassTest;

use ClassTest\Exception\ProphesizedObjectNotFoundException;
use ClassTest\TestTools;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophecy\ProphecyInterface;

/**
 * Class AbstractTestCase
 *
 * @author Clement Malet
 */
abstract class AbstractTestCase extends TestCase
{
    const DEFAULT_PROPHECY = 0;

    const DUMMY_PROPHECY   = 1;

    /**
     * Every ObjectProphecy is considered to be a mock here
     * The 'Dummy' keyword is only used to instantiate what we will then consider a mock
     *
     * @var ObjectProphecy[] $mocks
     */
    private $mocks = [];

    /**
     * @param string $mockName
     * @param bool $createIfNotFound
     * @return ObjectProphecy
     * @throws \ReflectionException
     */
    protected function getProphecy(string $mockName, bool $createIfNotFound = false): ProphecyInterface
    {
        if (isset($this->mocks[$mockName])) {
            $this->mocks[$mockName];
        }

        if ($createIfNotFound) {
            $this->addNewStub($mockName);
        } else {
            throw new ProphesizedObjectNotFoundException();
        }

        return $this->mocks[$mockName];
    }

    /**
     * @param string $prophecyName
     * @param string $methodName
     * @param array|null $arguments
     * @return MethodProphecy
     * @throws \ReflectionException
     */
    protected function getProphecyMethod(
        string $prophecyName,
        string $methodName,
        ?array $arguments = null
    ): MethodProphecy {
        return TestTools::getProphecyMethod($this->getProphecy($prophecyName), $methodName, $arguments);
    }

    /**
     * Extension of the prophesize() method that provides a way to initiate a dummy with "void" methods,
     * allowing all methods to take any arguments and to return null.
     *
     * Methods behavior can still be changed later on, though.
     *
     * @param string $classOrInterface
     * @param int $prophecyDummyType
     * @return ObjectProphecy
     * @throws \ReflectionException
     */
    protected function prophesize($classOrInterface = null, int $prophecyDummyType = self::DEFAULT_PROPHECY): ObjectProphecy
    {
        return $prophecyDummyType === self::DUMMY_PROPHECY ? parent::prophesize($classOrInterface) : $this->prophesizeDummy(
            $classOrInterface
        );
    }

    /**
     * Initiate a dummy with "void" methods, allowing all methods to take any arguments and to return null.
     *
     * @param string|null $class
     * @return ObjectProphecy
     * @throws \ReflectionException
     * @see prophesize
     */
    protected function prophesizeDummy(?string $class = null): ObjectProphecy
    {
        $prophecy = parent::prophesize($class);
        TestTools::setDummyProphecy($prophecy, $class);

        return $prophecy;
    }

    /**
     * @return ObjectProphecy[]
     */
    protected function getMocks(): iterable
    {
        return $this->mocks;
    }

    /**
     * @param ObjectProphecy[] $mocks
     */
    protected function setMocks(iterable $mocks): void
    {
        foreach ($mocks as $name => $mock) {
            $this->addMock($mock, $name);
        }
    }

    /**
     * Adds a mock object to the mocks collection
     *
     * @param ObjectProphecy $mock
     * @param string $keyName
     */
    protected function addMock(ObjectProphecy $mock, string $keyName): void
    {
        $this->mocks[$keyName] = $mock;
    }

    /**
     * Adds a mock from a class name, then returns it
     *
     * @param string $mockClassName
     * @param string|null $mockName
     * @return ObjectProphecy
     * @throws \ReflectionException
     */
    protected function addNewStub(string $mockClassName, ?string $mockName = null): ObjectProphecy
    {
        TestTools::assertIsProphesizable($mockClassName);
        $mock = $this->prophesize($mockClassName);

        $this->addMock($mock, $mockName === null ? $mockClassName : $mockName);

        return $mock;
    }

    /**
     * Adds a dummy from a class name
     *
     * @param string $dummyClassName
     * @param string|null $dummyName
     * @return ObjectProphecy
     * @throws \ReflectionException
     */
    protected function addNewDummy(string $dummyClassName, ?string $dummyName = null): ObjectProphecy
    {
        TestTools::assertIsProphesizable($dummyClassName);
        $dummy = $this->prophesizeDummy($dummyClassName);

        $this->addMock($dummy, $dummyName === null ? $dummyClassName : $dummyName);

        return $dummy;
    }
}
