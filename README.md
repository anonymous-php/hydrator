# hydrator

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

(new Hydrator())->hydrate($model, $data);

var_dump($model);
```

Result

```
object(Vehicle)#3 (2) {
  ["doors":"Vehicle":private]=>
  int(3)
  ["seats":"Vehicle":private]=>
  int(2)
}
```
