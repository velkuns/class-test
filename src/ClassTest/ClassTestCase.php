<?php

namespace CMalet\ClassTest;

use ClassTest\ClassTest\AbstractTestCase;
use ClassTest\Exception\NotProphesizableException;
use ClassTest\TestTools;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class ClassTestCase
 *
 * Helps to instantiate a clean class to use for test before every test, and provide a method to retrieve
 * its mocked parameters to quickly set test actions on these mocks
 *
 * @author Clement Malet
 */
abstract class ClassTestCase extends AbstractTestCase
{
    /**
     * Used to indicate that a class parameter is a string, and not a class to Prophecyze
     * Useful when a parameter matches a class name
     *
     * How to use it :
     * ['SomeString' => ClassTestCase::STRING_PARAMETER]
     */
    const STRING_PARAMETER = 'enforce_string_parameter';

    /** @var $testedClass */
    private $testedClass;

    /**
     * @return string The class name (::class) that is being tested and wished to be returned by
     *                the getTestedClass method
     */
    abstract protected function getTestedClassName();

    /**
     * @return array An ORDERED array of classes to be mocked (or values) that will be given as parameters to the
     *               constructor of the tested class.
     * @see ClassTestCase::setUp to see how different types of parameters are handled
     *
     */
    abstract protected function getTestedClassConstructorParameters();

    /**
     * Instantiate a clean class to use for tests, and stores its mocked parameters for re-use
     * with getMockedParameter to quickly set test actions on these mocks
     *
     * Parameters from getTestedClassParameters can be :
     *  - A string that match a class name, if so the class is mocked and a mock is given to the class constructor
     *  - A mock object, if so this mock is given to the class constructor, and the mock can be accessed with
     *    the getMockedParameter with the key of the value in the given array
     *  - Anything else, directly given as such to the class constructor
     */
    public function setUp()
    {
        $parameters = [];
        foreach ($this->getTestedClassConstructorParameters() as $parameterName => $parameter) {
            // Prevents problems with string parameters that match a class name that could be instantiated
            if ($parameter === self::STRING_PARAMETER) {
                $parameters[] = $parameterName;
                continue;
            }

            if ($parameter instanceof ObjectProphecy) {
                $this->addMock($parameter, $parameterName);
                $parameters[] = $parameter->reveal();
                continue;
            }

            try {
                TestTools::assertIsProphesizable($parameter);

                $prophecy = $this->prophesize($parameter);
                $this->addMock($prophecy, $parameter);
                $parameters[] = $prophecy->reveal();
            } catch (NotProphesizableException $exception) {
                // Not a prophesizable object, keep parameter as such
                $parameters[] = $parameter;
            }
        }

        $testedClass = $this->getTestedClass();
        $this->testedClass = new $testedClass(...$parameters);
    }

    /**
     * @return object
     */
    protected function getTestedClass()
    {
        return $this->testedClass;
    }
}
