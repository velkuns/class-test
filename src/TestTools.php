<?php

declare(strict_types=1);

namespace ClassTest;

use ClassTest\Exception\NotProphesizableException;
use Prophecy\Argument;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class TestTools
 *
 * Meant to be a collection of useful / hack methods that may help a lot while creating tests
 *
 * @author Clement Malet
 */
class TestTools
{
    /**
     * Allow to test a protected (or private) method, even if it is not meant to be possible
     * by PHPUnit
     *
     * @param object $object
     * @param string $method
     * @param array $args
     * @return mixed
     * @throws \ReflectionException
     */
    public static function callProtectedMethod($object, string $method, array $args)
    {
        $class = new \ReflectionClass(get_class($object));
        $method = $class->getMethod($method);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $args);
    }

    /**
     * Allow to force a protected (or private) property, even if it is not meant to be possible
     *
     * @param object $object
     * @param string $property
     * @param mixed $value
     * @return void
     * @throws \ReflectionException
     */
    public static function setProtectedProperty($object, string $property, $value): void
    {
        $property = new \ReflectionProperty(get_class($object), $property);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    /**
     * Retrieves the MethodProphecy from a prophecy for a given method name
     * (Allows to get a method from a string, instead of a direct method call as it is the case
     * with the basic usage of Prophecy, and so avoid potential method may not exist warnings)
     *
     * @param ObjectProphecy $prophecy
     * @param string $methodName
     * @param array|null $arguments
     * @param mixed|null $return
     * @return mixed|MethodProphecy
     */
    public static function getProphecyMethod(
        ObjectProphecy $prophecy,
        string $methodName,
        ?array $arguments = null,
        $return = null
    ): MethodProphecy {
        if ($arguments === null) {
            $arguments = [Argument::cetera()];
        }

        $methodProphecies = $prophecy->getMethodProphecies($methodName);
        if ($methodProphecies === []) {
            /** @var MethodProphecy $methodProphecy */
            $methodProphecy = $prophecy->$methodName();
            $prophecy->addMethodProphecy($methodProphecy);
        } else {
            $methodProphecy = array_shift($methodProphecies);
        }

        $methodProphecy->withArguments(new Argument\ArgumentsWildcard($arguments));
        if (!$methodProphecy->hasReturnVoid()) {
            $methodProphecy->willReturn($return);
        }

        return $methodProphecy;
    }

    /**
     * Sets methods of a prophecy dummy so that they accept any arguments, and always return null
     *
     * @param ObjectProphecy $prophecy
     * @param string $prophecyName
     * @return void
     * @throws \ReflectionException
     */
    public static function setDummyProphecy(ObjectProphecy $prophecy, string $prophecyName): void
    {
        $reflectionClass = new \ReflectionClass($prophecyName);
        foreach ($reflectionClass->getMethods() as $method) {
            $methodName = $method->getName();

            if (strpos($methodName, '__') === 0) {
                continue;
            }

            self::getProphecyMethod($prophecy, $methodName);
        }
    }

    /**
     * @param object $prophesizedObject
     * @throws \InvalidArgumentException
     */
    public static function assertIsObjectProphecy($prophesizedObject): void
    {
        if (($prophesizedObject instanceof ObjectProphecy) === false) {
            throw new \InvalidArgumentException(
                'Expecting array of ObjectProphecy, got an instance of ' . get_class($prophesizedObject) . ' instead.'
            );
        }
    }

    /**
     * "Assert" that a given class name is prophesizable in the sense that a prophesize call would result
     * on a random null Prophecy
     *
     * @param mixed $className
     * @throws NotProphesizableException
     */
    public static function assertIsProphesizable($className): void
    {
        if (!is_string($className)) {
            throw new NotProphesizableException();
        }

        try {
            new \ReflectionClass($className);
        } catch (\ReflectionException $exception) {
            throw new NotProphesizableException();
        }
    }
}
