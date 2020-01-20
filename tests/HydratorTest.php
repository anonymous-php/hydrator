<?php

use Anonymous\Hydrator\Hydrator;
use PHPUnit\Framework\TestCase;


final class HydratorTest extends TestCase
{

    public function testCanHydrateDefinedStsClassProperties()
    {
        $object = (object)['property' => null];

        $this->assertNull($object->property);
        $this->assertFalse(property_exists($object, 'undefinedProperty'));

        $data = ['property' => 'value', 'undefinedProperty' => 'value2'];
        (new Hydrator())->hydrate($object, $data);

        $this->assertEquals($object->property, 'value');
        $this->assertFalse(property_exists($object, 'undefinedProperty'));
    }

    public function testCanHydrateUndefinedStsClassProperties()
    {
        $object = (object)['property' => null];

        $this->assertNull($object->property);
        $this->assertFalse(property_exists($object, 'undefinedProperty'));

        $data = ['property' => 'value', 'undefinedProperty' => 'value2'];
        (new Hydrator())->hydrate($object, $data, true);

        $this->assertEquals($object->property, 'value');
        $this->assertTrue(property_exists($object, 'undefinedProperty'));
        $this->assertEquals($object->undefinedProperty, 'value2');
    }

    public function testCanHydratePrivateProperties()
    {
        $object = new TestModel();

        $this->assertNull($object->publicProperty);
        $this->assertNull($object->getPrivateProperty());
        $this->assertNull($object->getProtectedProperty());

        $data = ['publicProperty' => 'public', 'privateProperty' => 'private', 'protectedProperty' => 'protected'];

        (new Hydrator())->hydrate($object, $data);

        $this->assertEquals($object->publicProperty, 'public');
        $this->assertEquals($object->getPrivateProperty(), 'private');
        $this->assertEquals($object->getProtectedProperty(), 'protected');
    }

    public function testCanHydratePropertiesUsingSetters()
    {
        $object = new TestModel();

        $this->assertNull($object->getCreatedAt());

        (new Hydrator())->hydrate($object, ['created_at' => '2020-01-01']);
        $this->assertInstanceOf(DateTimeImmutable::class, $object->getCreatedAt());
        $this->assertEquals($object->getCreatedAt()->format('Y-m-d'), '2020-01-01');

        (new Hydrator())->hydrate($object, ['createdAt' => '2020-01-01']);
        $this->assertEquals($object->getCreatedAt()->format('Y-m-d'), '2020-01-01');
    }

}

class TestModel
{

    public $publicProperty;
    private $privateProperty;
    protected $protectedProperty;
    private $created_at;


    public function getPrivateProperty()
    {
        return $this->privateProperty;
    }

    public function getProtectedProperty()
    {
        return $this->protectedProperty;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setCreatedAt($date)
    {
        $this->created_at = new DateTimeImmutable($date);
    }

}
