<?php

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
     * @param $object
     * @param $method
     * @param $args
     * @return mixed
     * @throws \ReflectionException
     */
    public static function callProtectedMethod($object, $method, $args)
    {
        $class = new \ReflectionClass(get_class($object));
        $method = $class->getMethod($method);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $args);
    }

    /**
     * Allow to force a protected (or private) property, even if it is not meant to be possible
     *
     * @param $object
     * @param $property
     * @param $value
     * @throws \ReflectionException
     */
    public static function setProtectedProperty($object, $property, $value)
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
     * @param                $methodName
     * @param array|null $arguments
     * @param                $return
     * @return MethodProphecy
     */
    public static function getProphecyMethod(ObjectProphecy $prophecy, $methodName, $arguments = null, $return = null)
    {
        if ($arguments === null) {
            $arguments = Argument::cetera();
        }

        $methodProphecies = $prophecy->getMethodProphecies($methodName);
        if ($methodProphecies === []) {
            /** @var MethodProphecy $methodProphecy */
            $methodProphecy = $prophecy->$methodName();
            $prophecy->addMethodProphecy($methodProphecy);
        } else {
            $methodProphecy = array_shift($methodProphecies);
        }

        $methodProphecy->withArguments(new Argument\ArgumentsWildcard([$arguments]));
        if (!$methodProphecy->hasReturnVoid()) {
            $methodProphecy->willReturn($return);
        }

        return $methodProphecy;
    }

    /**
     * Sets methods of a prophecy dummy so that they accept any arguments, and always return null
     *
     * @param ObjectProphecy $prophecy
     * @param                $prophecyName
     * @throws
     */
    public static function setDummyProphecy(ObjectProphecy $prophecy, $prophecyName)
    {
        $reflectionClass = new \ReflectionClass($prophecyName);
        foreach ($reflectionClass->getMethods() as $method) {
            $methodName = $method->getName();

            if (strpos($methodName, '__') === 0) {
                continue;
            }

            self::getProphecyMethod($prophecy, $methodName, null, null);
        }
    }

    /**
     * @param $prophesizedObject
     * @throws \InvalidArgumentException
     */
    public static function assertIsObjectProphecy($prophesizedObject)
    {
        if (($prophesizedObject instanceof ObjectProphecy) === false) {
            throw new \InvalidArgumentException('Expecting array of ObjectProphecy, got an instance of ' . get_class($prophesizedObject) . ' instead.');
        }
    }

    /**
     * "Assert" that a given class name is prophesizable in the sense that a prophesize call would result
     * on a random null Prophecy
     *
     * @param $className
     * @throws NotProphesizableException
     */
    public static function assertIsProphesizable($className)
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
