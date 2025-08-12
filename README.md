# Class test

[![Current version](https://img.shields.io/packagist/v/c-malet/class-test.svg?logo=composer)](https://packagist.org/packages/velkuns/pipeline)
[![Supported PHP version](https://img.shields.io/static/v1?logo=php&label=PHP&message=^8.1&color=777bb4)](https://packagist.org/packages/c-malet/class-test)
![CI](https://github.com/C-Malet/class-test/workflows/CI/badge.svg)

PHP testing library focused on testing classes mocking all of their constructor parameters, using Prophecy.

The main objective is to make testing a class quicker by automatizing the tested class instantiation with dummy parameters, and retrieve a whole new instance for each test.
Particularly useful to test classes with many objects as constructor parameters that you don't want to be executed, such as helpers, loggers, services, repositories, ...

_This library is based on the PHP object mocking framework Prophecy, and requires to know how to test with it, [learn more about it here](https://github.com/phpspec/prophecy)_

## Simple example

```php
class TestSomeClass extends ClassTestCase {
    public function getTestedClassName() {
        return SomeClass::class;
    }
    
    public function getTestedClassConstructorParameters() {
        return [
            SomeRepository::class,
            SomeService::class,
            Logger::class,
            DbInterface::class,
            'someString'
        ];
    }
    
    public function testSomething() {
        // Retrieve a new instance of SomeClass with dummy constructor parameters
        // that can handle any parameters and return null all the time
        $someClass = $this->getTestedClass();
        
        $result = $someClass->doSomething('someText', 3);
        $this->assert(...);
    }
}
```

## Installation 

#### Prerequisites 

Requires PHP 8.1 or greater and PHPUnit ^9.0 || ^10.0

#### Composer

Run composer command `composer require c-malet/class-test`

## Usage

#### Test set up

Set up the test class for the tested class as such :

Extend the ClassTestCase class (which extends the PHPUnit TestCase)
```php
class TestSomeClass extends ClassTestCase {}

```

Implement the two mandatory methods defining the tested class :
```php
public function getTestedClassName() {
    return SomeClass::class; // or the fully qualified class name
}

public function getTestedClassConstructorParameters() {
    return [
        SomeClass::class,
        SomeInterface::class,
        
        // An object can be provided directly, it will be kept unchanged
        $someObject, 
        
        // If you want to provide several instances of the same class,
        // or if you want to force a key to retrieve mocks later on, 
        // simply specify a key
        'Logger1' => LoggerInterface::class, 
        'Logger2' => LoggerInterface::class,
        
        // string, integer, array, ... can still be used directly
        'someString',
        12345,
        
        // If a string would match a class name for instance, you can still
        // force it as a string parameter this way
        'SomeClass' => ClassTestCase::STRING_PARAMETER
    ]
}
```

#### Testing

For each test, a whole new instance of the tested class is created and can be retrieved :

```php
$someClass = $this->getTestedClass();
```

_You can override the `getTestedClass` method in each test case to inform your IDE the class type of `$someClass`_

It returns a "real" instance of the tested class, it is not a mock, and the whole code of this class will be executed.

Contrariwise, every constructor parameter provided that matches an instantiatable class is transformed into a revealed Prophecy, in such a way that they will automatically handle any parameter and always return null by default.

These Prophecy mocks can be retrieved anytime during tests, to do anything you could do with Prophecy alone, using the `getMock` method,
allowing you to override the default dummy set up as needed for your tests.

```php
$this->getProphecy(SomeRepository::class)->someMethod()->shouldBeCalled(1);
```  

_Mocks can be retrieved by their class name or by key, as described earlier in the 'Test set up' part_

If your IDE fails to resolve the methods from the class name and gives warning, you can also get ProphecyMethod objects this way :

```php
$this->getProphecyMethod(SomeRepository::class, 'someMethod')->shouldBeCalled(1);
```

If you wish to use the internal mocks container for any other class to mock, for instance mocks you'd want to use in your tested methods, you can also create mocks and dummies that can be retrieved as other mocks with `addNewMock` or `addNewDummy`

```php
$this->addNewMock(SomeClassToMock::class);
$this->addNewDummy(SomeClassToDummy::class);
...
$this->getProphecyMock(SomeClassToMock::class)->myMethod()->willReturn(true);
```

## Full example 

```php
class TestPizzaCooker extends ClassTestCase {
    public function getTestedClassName() {
        return PizzaCooker::class;
    }
    
    public function getTestedClassConstructorParameters() {
        OvenInterface::class,
        IngredientPicker::class,
        PizzaFolder::class,
        TimerHelper::class
    }
    
    public function testPizzaShouldAlwaysBeBaked() {
        $pizzaCooker = $this->getTestedClass();
        $pizzaCooker->cookPizza('margerhita', 'XL')

        $this->getProphecy(OvenInterface::class)->bake()->shouldHaveBeenCalled(1);
    }
    
    public function testPizzaCalzoneShouldBeFolded() {
        $pizza = $this->getProphecy(OvenInterface::class)->bake()->willReturn(
            $this->addNewDummy(Pizza::class)->reveal()
        );

        // Folder should fold the previously baked pizza 
        $this->getProphecyMethod(FolderInterface::class, 'fold')->with([$pizza->reveal()]);

        $pizzaCooker = $this->getTestedClass();
        $pizzaCooker->cookPizza('calzone')
        
        // A calzone should always be folded
        $this->getProphecy(FolderInterface::class)->bake()->shouldHaveBeenCalled(1);
    }
}
```


## Contributing

See the [CONTRIBUTING](CONTRIBUTING.md) file.


### Install / update project

You can install project with the following command:
```bash
make install
```

And update with the following command:
```bash
make update
```

Check dependencies:
```bash
make deps
```

NB: For the components, the `composer.lock` file is not committed.

### Testing & CI (Continuous Integration)

#### Tests
You can run unit tests (with coverage) on your side with following command:
```bash
make tests
```

For prettier output (but without coverage), you can use the following command:
```bash
make testdox # run tests without coverage reports but with prettified output
```

#### Code Style
You also can run code style check with following commands:
```bash
make phpcs-check
```

You also can run code style fixes with following commands:
```bash
make phpcs-fix
```

#### Static Analysis
To perform a static analyze of your code (with phpstan, lvl max at default), you can use the following command:
```bash
make analyze
```

To ensure you code still compatible with current supported version at Deezer and futures versions of php, you need to
run the following commands (both are required for full support):

Minimal supported version:
```bash
make phpmin-compatibility
```

Maximal supported version:
```bash
make phpmax-compatibility
```

#### CI Simulation
And the last "helper" commands, you can run before commit and push, is:
```bash
make ci  
```
