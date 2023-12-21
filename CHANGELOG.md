# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

```
## [template]
[template]: https://github.com/C-Malet/class-test/compare/4.0.0...master
### Changed
### Added
### Removed
```

## [4.0.0] - 2023-12
[4.0.0]: https://github.com/C-Malet/class-test/compare/4.0.0...3.0.1
### Removed
- Remove PHPCS
- Drop support of PHP 7.3, 7.4 & 8.0
### Added
- Add make file
- Add phpcov 9+ 
- phpstan 1.10+
- Add CI Actions
- Add php-cs-fixer
### Changed
- Update phpunit to 10+
- Move to root src/ the AbstractTestClass + ClassTestCase
- Improve phpdoc for phpstan
- Fix errors from phpunit update
- Add missing type hint
- Update namespace for tests

--- 

# [3.0.1] - 2021-03-09
### Changed
Prevent prophecy method arguments from being wrapped in an array

Test sample

```php
$prophecy->reveal()->doBar('foo');
TestTools::getProphecyMethod($prophecy, 'doBar', ['foo'])->shouldHaveBeenCalled();
```

Previous behavior:

```
Expected exactly 1 calls that match:
  Double\P16->doBar(exact(['foo']))
but none were made.
Recorded `doBar(...)` calls:
  - doBar('foo')
```

New behavior:

```
OK
```

## [3.0.0] - 2021-02-11
### Changed
Now require PHP 7.3 or PHP 8
Now require phpunit 9.1+
Fix deprecation notice on TestCase::phophesize method


----

## [2.0.1] - 2021-03-09
### Changed
Prevent prophecy method arguments from being wrapped in an array

Test sample

```php
$prophecy->reveal()->doBar('foo');
TestTools::getProphecyMethod($prophecy, 'doBar', ['foo'])->shouldHaveBeenCalled();
```

Previous behavior:

```
Expected exactly 1 calls that match:
  Double\P16->doBar(exact(['foo']))
but none were made.
Recorded `doBar(...)` calls:
  - doBar('foo')
```

New behavior:

```
OK
```

## [2.0.0] - 2021-01-13
### Changed
- Now require PHP 7.1
-Now require phpunit 7.5+
-Update phpunit.xml.dist
-Update composer.json according to the new requirements
### Added
- Use strict mode for each file
-Force type hinting when possible

----

## [1.0.10] - 2021-03-09
### Changed
Prevent prophecy method arguments from being wrapped in an array

Test sample

```php
$prophecy->reveal()->doBar('foo');
TestTools::getProphecyMethod($prophecy, 'doBar', ['foo'])->shouldHaveBeenCalled();
```

Previous behavior:

```
Expected exactly 1 calls that match:
  Double\P16->doBar(exact(['foo']))
but none were made.
Recorded `doBar(...)` calls:
  - doBar('foo')
```

New behavior:

```
OK
```

## [1.x] - 2020
### Added
- Init repo
### Changed
- Some fixes
