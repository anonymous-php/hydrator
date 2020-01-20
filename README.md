# Hydrator [![Build Status](https://travis-ci.org/anonymous-php/hydrator.svg?branch=master)](https://travis-ci.org/anonymous-php/hydrator) [![Latest Stable Version](https://poser.pugx.org/anonymous-php/hydrator/v/stable)](https://packagist.org/packages/anonymous-php/hydrator) [![Total Downloads](https://poser.pugx.org/anonymous-php/hydrator/downloads)](https://packagist.org/packages/anonymous-php/hydrator?format=flat) [![License](https://poser.pugx.org/anonymous-php/hydrator/license)](https://packagist.org/packages/anonymous-php/hydrator)

This library gives you the possibility to hydrate your anemic models even if they contains private properties.

### Example

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Anonymous\Hydrator\Hydrator;

class Vehicle
{
    private $doors;
    private $seats;
}

$model = new Vehicle();

$data = [
    'doors' => 3,
    'seats' => 2,
];

Hydrator::hydrate($model, $data);

var_dump($model);

$properties = Hydrator::toArray($model);

var_dump($properties);
```

Result

```
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
```

### Extending

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Anonymous\Hydrator\Hydrator;

class CustomHydrator extends Hydrator
{

    protected static function getGetterName(string $property): string
    {
        return 'get' . parent::prepareName($property) . 'Attribute';
    }

    protected static function getSetterName(string $property): string
    {
        return 'set' . parent::prepareName($property) . 'Attribute';
    }

}
```


