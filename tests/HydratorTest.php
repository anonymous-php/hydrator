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
        Hydrator::hydrate($object, $data);

        $this->assertEquals($object->property, 'value');
        $this->assertFalse(property_exists($object, 'undefinedProperty'));
    }

    public function testCanHydrateUndefinedStsClassProperties()
    {
        $object = (object)['property' => null];

        $this->assertNull($object->property);
        $this->assertFalse(property_exists($object, 'undefinedProperty'));

        $data = ['property' => 'value', 'undefinedProperty' => 'value2'];
        Hydrator::hydrate($object, $data, true);

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

        Hydrator::hydrate($object, $data);

        $this->assertEquals($object->publicProperty, 'public');
        $this->assertEquals($object->getPrivateProperty(), 'private');
        $this->assertEquals($object->getProtectedProperty(), 'protected');
    }

    public function testCanHydratePropertiesUsingSetters()
    {
        $object = new TestModel();

        $this->assertNull($object->getCreatedAt());

        Hydrator::hydrate($object, ['created_at' => '2020-01-01']);
        $this->assertInstanceOf(DateTimeImmutable::class, $object->getCreatedAt());
        $this->assertEquals($object->getCreatedAt()->format('Y-m-d'), '2020-01-01');

        Hydrator::hydrate($object, ['createdAt' => '2020-01-01']);
        $this->assertEquals($object->getCreatedAt()->format('Y-m-d'), '2020-01-01');
    }

    public function testCanDehydrateProperties()
    {
        $object = new TestModel();
        $data = ['publicProperty' => 'public', 'privateProperty' => 'private', 'protectedProperty' => 'protected'];

        Hydrator::hydrate($object, $data);

        $properties = Hydrator::toArray($object);
        $this->assertArraySubset($data, $properties);

        $properties = Hydrator::toArray($object, ['publicProperty']);
        $this->assertEquals(['publicProperty' => 'public'], $properties);
    }

    public function testCanDehydratePropertiesUsingGetters()
    {
        $object = new TestModel();
        $data = ['upperOnGet' => 'upper'];

        Hydrator::hydrate($object, $data);

        $properties = Hydrator::toArray($object, ['upperOnGet']);
        $this->assertEquals(['upperOnGet' => 'UPPER'], $properties);
    }

    public function testCanDehydrateUndefinedProperties()
    {
        $object = new TestModel();
        $properties = Hydrator::toArray($object, ['undefinedProperty']);
        $this->assertEquals(['undefinedProperty' => null], $properties);
    }

}

class TestModel
{

    public $publicProperty;
    private $privateProperty;
    protected $protectedProperty;
    private $created_at;
    private $upperOnGet;

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

    public function getUpperOnGet()
    {
        return strtoupper($this->upperOnGet);
    }

    public function setCreatedAt($date)
    {
        $this->created_at = new DateTimeImmutable($date);
    }

}
