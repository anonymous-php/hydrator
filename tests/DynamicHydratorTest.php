<?php

use Anonymous\Hydrator\DynamicHydrator as Hydrator;
use PHPUnit\Framework\TestCase;


final class DynamicHydratorTest extends TestCase
{

    public function testCanHydrateDefinedStdClassProperties()
    {
        $hydrator = new Hydrator();
        $object = (object)['property' => null];

        $this->assertNull($object->property);
        $this->assertFalse(property_exists($object, 'undefinedProperty'));

        $data = ['property' => 'value', 'undefinedProperty' => 'value2'];
        $hydrator->hydrate($object, $data, false);

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
        $hydrator->hydrate($object, $data, true);

        $this->assertEquals($object->property, 'value');
        $this->assertTrue(property_exists($object, 'undefinedProperty'));
        $this->assertEquals($object->undefinedProperty, 'value2');
    }

    public function testCanHydratePrivateProperties()
    {
        $hydrator = new Hydrator();
        $object = new DynamicTestModel();

        $this->assertNull($object->publicProperty);
        $this->assertNull($object->getPrivateProperty());
        $this->assertNull($object->getProtectedProperty());

        $data = ['publicProperty' => 'public', 'privateProperty' => 'private', 'protectedProperty' => 'protected'];

        $hydrator->hydrate($object, $data);

        $this->assertEquals($object->publicProperty, 'public');
        $this->assertEquals($object->getPrivateProperty(), 'private');
        $this->assertEquals($object->getProtectedProperty(), 'protected');
    }

    public function testCanHydratePropertiesUsingSetters()
    {
        $hydrator = new Hydrator();
        $object = new DynamicTestModel();

        $this->assertNull($object->getCreatedAt());

        $hydrator->hydrate($object, ['created_at' => '2020-01-01']);
        $this->assertInstanceOf(DateTimeImmutable::class, $object->getCreatedAt());
        $this->assertEquals($object->getCreatedAt()->format('Y-m-d'), '2020-01-01');

        $hydrator->hydrate($object, ['createdAt' => '2020-01-01']);
        $this->assertEquals($object->getCreatedAt()->format('Y-m-d'), '2020-01-01');
    }

    public function testCanDehydrateProperties()
    {
        $hydrator = new Hydrator();
        $object = new DynamicTestModel();
        $data = ['publicProperty' => 'public', 'privateProperty' => 'private', 'protectedProperty' => 'protected'];

        $hydrator->hydrate($object, $data);

        $properties = $hydrator->extract($object);
        $this->assertArraySubset($data, $properties);

        $properties = $hydrator->extract($object, ['publicProperty']);
        $this->assertEquals(['publicProperty' => 'public'], $properties);
    }

    public function testCanDehydratePropertiesUsingGetters()
    {
        $hydrator = new Hydrator();
        $object = new DynamicTestModel();
        $data = ['upperOnGet' => 'upper'];

        $hydrator->hydrate($object, $data);

        $properties = $hydrator->extract($object, ['upperOnGet']);
        $this->assertEquals(['upperOnGet' => 'UPPER'], $properties);
    }

    public function testCanDehydrateUndefinedProperties()
    {
        $hydrator = new Hydrator();
        $object = new DynamicTestModel();
        $properties = $hydrator->extract($object, ['undefinedProperty']);
        $this->assertEquals(['undefinedProperty' => null], $properties);
    }

}

class DynamicTestModel
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
