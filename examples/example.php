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

