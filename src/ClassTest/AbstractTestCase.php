<?php

namespace ClassTest\ClassTest;

use ClassTest\Exception\ProphesizedObjectNotFoundException;
use ClassTest\TestTools;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class AbstractTestCase
 *
 * @author Clement Malet
 */
abstract class AbstractTestCase extends TestCase
{
    const DEFAULT_PROPHECY = 0;
    const DUMMY_PROPHECY = 1;

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
     */
    protected function getProphecy($mockName, $createIfNotFound = false)
    {
        if (!isset($this->mocks[$mockName])) {
            if ($createIfNotFound) {
                $this->addNewStub($mockName);
            } else {
                throw new ProphesizedObjectNotFoundException();
            }
        }

        return $this->mocks[$mockName];
    }

    /**
     * @param $prophecyName
     * @param $methodName
     * @param $arguments
     * @return \Prophecy\Prophecy\MethodProphecy
     */
    protected function getProphecyMethod($prophecyName, $methodName, $arguments = null)
    {
        return TestTools::getProphecyMethod($this->getProphecy($prophecyName), $methodName, $arguments);
    }

    /**
     * Extension of the prophesize() method that provides a way to initiate a dummy with "void" methods,
     * allowing all methods to take any arguments and to return null.
     *
     * Methods behavior can still be changed later on, though.
     *
     * @param string $class
     * @param int  $prophecyDummyType
     * @return ObjectProphecy
     */
    protected function prophesize($class = null, $prophecyDummyType = self::DEFAULT_PROPHECY)
    {
        return $prophecyDummyType === self::DUMMY_PROPHECY ?
            parent::prophesize($class) :
            $this->prophesizeDummy($class);
    }

    /**
     * Initiate a dummy with "void" methods, allowing all methods to take any arguments and to return null.
     *
     * @param null $class
     * @return ObjectProphecy
     * @see prophesize
     */
    protected function prophesizeDummy($class = null)
    {
        return $this->prophesize($class, self::DUMMY_PROPHECY);
    }

    /**
     * @return ObjectProphecy[]
     */
    protected function getMocks()
    {
        return $this->mocks;
    }

    /**
     * @param ObjectProphecy[] $mocks
     */
    protected function setMocks($mocks)
    {
        foreach ($mocks as $mock) {
            TestTools::assertIsObjectProphecy($mock);
        }

        $this->mocks = $mocks;
    }

    /**
     * Adds a mock object to the mocks collection
     *ad
     * @param ObjectProphecy $mock
     * @param string         $keyName
     */
    protected function addMock($mock, $keyName)
    {
        TestTools::assertIsObjectProphecy($mock);

        $this->mocks[$keyName] = $mock;
    }

    /**
     * Adds a mock from a class name, then returns it
     *
     * @param string $mockClassName
     * @param string $mockName
     * @return ObjectProphecy
     */
    protected function addNewStub($mockClassName, $mockName = null)
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
     * @param string $dummyName
     * @return ObjectProphecy
     */
    protected function addNewDummy($dummyClassName, $dummyName = null)
    {
        TestTools::assertIsProphesizable($dummyClassName);
        $dummy = $this->prophesizeDummy($dummyClassName);

        $this->addMock($dummy, $dummyName === null ? $dummyClassName : $dummyName);

        return $dummy;
    }
}
