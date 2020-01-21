# Hydrator [![Build Status](https://travis-ci.org/anonymous-php/hydrator.svg?branch=master)](https://travis-ci.org/anonymous-php/hydrator) [![Latest Stable Version](https://poser.pugx.org/anonymous-php/hydrator/v/stable)](https://packagist.org/packages/anonymous-php/hydrator) [![Total Downloads](https://poser.pugx.org/anonymous-php/hydrator/downloads)](https://packagist.org/packages/anonymous-php/hydrator?format=flat) [![License](https://poser.pugx.org/anonymous-php/hydrator/license)](https://packagist.org/packages/anonymous-php/hydrator)

This library gives you the possibility to hydrate your anemic models even if they contains private properties.

## Installation

```
composer require anonymous-php/hydrator
```

## Example

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Anonymous\Hydrator\Hydrator;

class Vehicle
{
    private $doors;
    private $seats;
}

$hydrator = new Hydrator();

$model = new Vehicle();
$data = ['doors' => 3, 'seats' => 2];

$hydrator->hydrate($data, $model);

var_dump($model);

$properties = $hydrator->extract($model);

var_dump($properties);

/*
    object(Vehicle)#3 (2) {
      ["doors":"Vehicle":private]=>
      int(3)
      ["seats":"Vehicle":private]=>
      int(2)
    }
    array(2) {
      ["doors"]=>
      int(3)
      ["seats"]=>
      int(2)
    }
*/
```

## Extending

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Anonymous\Hydrator\DynamicHydrator;

class CustomHydrator extends DynamicHydrator
{

    protected function getGetterName(string $property): string
    {
        return 'get' . $this->prepareName($property) . 'Attribute';
    }

    protected function getSetterName(string $property): string
    {
        return 'set' . $this->prepareName($property) . 'Attribute';
    }

}
```

## Benchmark

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Anonymous\Hydrator\Hydrator;
use Anonymous\Hydrator\DynamicHydrator;

class Example
{
    public $foo;
    public $bar;
    public $baz;
    public function setFoo($foo) { $this->foo = $foo; }
    public function setBar($bar) { $this->bar = $bar; }
    public function setBaz($baz) { $this->baz = $baz; }
    public function getFoo() { return $this->foo; }
    public function getBar() { return $this->bar; }
    public function getBaz() { return $this->baz; }
}

$model = new Example();
$data = ['foo' => 1, 'bar' => 2, 'baz' => 3];

$iterationsCount = 10000;

$hydrator = new Hydrator();

$start = microtime(true);
for ($i = 0; $i < $iterationsCount; $i++) {
    $hydrator->hydrate($data, $model);
}
$stop = microtime(true);

echo 'Hydrate: ', ($stop - $start), PHP_EOL;

$start = microtime(true);
for ($i = 0; $i < $iterationsCount; $i++) {
    $hydrator->extract($model);
}
$stop = microtime(true);

echo 'Extract: ', ($stop - $start), PHP_EOL;

$hydrator = new DynamicHydrator();

$start = microtime(true);
for ($i = 0; $i < $iterationsCount; $i++) {
    $hydrator->hydrate($data, $model);
}
$stop = microtime(true);

echo 'Hydrate using setters: ', ($stop - $start), PHP_EOL;

$start = microtime(true);
for ($i = 0; $i < $iterationsCount; $i++) {
    $hydrator->extract($model);
}
$stop = microtime(true);

echo 'Extract using getters: ', ($stop - $start), PHP_EOL;

/*
    Hydrate: 0.0046491622924805
    Extract: 0.0065388679504395
    Hydrate using setters: 0.011441946029663
    Extract using getters: 0.013833999633789
 */
```
