<?php

declare(strict_types=1);

namespace ClassTest;

use ClassTest\Exception\NotProphesizableException;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class ClassTestCase
 *
 * Helps to instantiate a clean class to use for test before every test, and provide a method to retrieve
 * its mocked parameters to quickly set test actions on these mocks
 *
 * @author Clement Malet
 *
 * @template T of object
 * @extends AbstractTestCase<T>
 */
abstract class ClassTestCase extends AbstractTestCase
{
    /**
     * Used to indicate that a class parameter is a string, and not a class to Prophesize
     * Useful when a parameter matches a class name
     *
     * How to use it :
     * ['SomeString' => ClassTestCase::STRING_PARAMETER]
     */
    public const STRING_PARAMETER = 'enforce_string_parameter';

    /** @var T $testedClass */
    private object $testedClass;

    /**
     * @return class-string<T> The class name (::class) that is being tested and wished to be returned by
     *                the getTestedClass method
     */
    abstract protected function getTestedClassName(): string;

    /**
     * @return array<mixed> An ORDERED array of classes to be mocked (or values) that will be given as parameters to the
     *               constructor of the tested class.
     * @see ClassTestCase::setUp to see how different types of parameters are handled
     *
     */
    abstract protected function getTestedClassConstructorParameters(): array;

    /**
     * Instantiate a clean class to use for tests, and stores its mocked parameters for re-use
     * with getMockedParameter to quickly set test actions on these mocks
     *
     * Parameters from getTestedClassParameters can be :
     *  - A string that match a class name, if so the class is mocked and a mock is given to the class constructor
     *  - A mock object, if so this mock is given to the class constructor, and the mock can be accessed with
     *    the getMockedParameter with the key of the value in the given array
     *  - Anything else, directly given as such to the class constructor
     *
     * @throws \ReflectionException
     */
    public function setUp(): void
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
                /** @var class-string<T> $parameter */
                TestTools::assertIsProphesizable($parameter);

                $prophecy = $this->prophesize($parameter, AbstractTestCase::DUMMY_PROPHECY);
                $this->addMock($prophecy, $parameter);
                $parameters[] = $prophecy->reveal();
            } catch (NotProphesizableException $exception) {
                // Not a prophesizable object, keep parameter as such
                $parameters[] = $parameter;
            }
        }

        $testedClass = $this->getTestedClassName();
        $this->testedClass = new $testedClass(...$parameters);
    }

    /**
     * @return T
     */
    public function getTestedClass(): object
    {
        return $this->testedClass;
    }
}
