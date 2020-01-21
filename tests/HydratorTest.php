<?php

use Anonymous\Hydrator\Hydrator;
use PHPUnit\Framework\TestCase;


final class HydratorTest extends TestCase
{

    public function testCanHydrateDefinedStdClassProperties()
    {
        $hydrator = new Hydrator();
        $object = (object)['property' => null];

        $this->assertNull($object->property);
        $this->assertFalse(property_exists($object, 'undefinedProperty'));

        $data = ['property' => 'value', 'undefinedProperty' => 'value2'];
        $hydrator->hydrate($data, $object, false);

        $this->assertEquals($object->property, 'value');
        $this->assertFalse(property_exists($object, 'undefinedProperty'));
    }

    public function testCanHydrateUndefinedStdClassProperties()
    {
        $hydrator = new Hydrator();
        $object = (object)['property' => null];

        $this->assertNull($object->property);
        $this->assertFalse(property_exists($object, 'undefinedProperty'));

        $data = ['property' => 'value', 'undefinedProperty' => 'value2'];
        $hydrator->hydrate($data, $object, true);

        $this->assertEquals($object->property, 'value');
        $this->assertTrue(property_exists($object, 'undefinedProperty'));
        $this->assertEquals($object->undefinedProperty, 'value2');
    }

    public function testCanHydratePrivateProperties()
    {
        $hydrator = new Hydrator();
        $object = new TestModel();

        $this->assertNull($object->publicProperty);
        $this->assertNull($object->getPrivateProperty());
        $this->assertNull($object->getProtectedProperty());

        $data = ['publicProperty' => 'public', 'privateProperty' => 'private', 'protectedProperty' => 'protected'];

        $hydrator->hydrate($data, $object);

        $this->assertEquals($object->publicProperty, 'public');
        $this->assertEquals($object->getPrivateProperty(), 'private');
        $this->assertEquals($object->getProtectedProperty(), 'protected');
    }

    public function testCanDehydrateProperties()
    {
        $hydrator = new Hydrator();
        $object = new TestModel();
        $data = ['publicProperty' => 'public', 'privateProperty' => 'private', 'protectedProperty' => 'protected'];

        $hydrator->hydrate($data, $object);

        $properties = $hydrator->extract($object);
        $this->assertArraySubset($data, $properties);

        $properties = $hydrator->extract($object, ['publicProperty']);
        $this->assertEquals(['publicProperty' => 'public'], $properties);
    }

    public function testCanDehydrateUndefinedProperties()
    {
        $hydrator = new Hydrator();
        $object = new TestModel();
        $properties = $hydrator->extract($object, ['undefinedProperty']);
        $this->assertEquals(['undefinedProperty' => null], $properties);
    }

}

class TestModel
{

    public $publicProperty;
    private $privateProperty;
    protected $protectedProperty;

    public function getPrivateProperty()
    {
        return $this->privateProperty;
    }

    public function getProtectedProperty()
    {
        return $this->protectedProperty;
    }

}
