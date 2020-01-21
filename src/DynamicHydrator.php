<?php

namespace Anonymous\Hydrator;


use Closure;

/**
 * Class DynamicHydrator
 * @package Anonymous\Hydrator
 */
class DynamicHydrator extends Hydrator
{

    /**
     * @var Closure
     */
    protected $getterClosure;

    /**
     * @var Closure
     */
    protected $setterClosure;

    /**
     * @var array
     */
    protected $gettersNames = [];

    /**
     * @var array
     */
    protected $settersNames = [];


    /**
     * @inheritDoc
     */
    protected function getHydrateClosure(): Closure
    {
        if (null === $this->setterClosure) {
            $this->setterClosure = function (string $property) {
                if (!array_key_exists($property, $this->settersNames)) {
                    $this->settersNames[$property] = $this->getSetterName($property);
                }

                return $this->settersNames[$property];
            };
        }

        $setter = $this->setterClosure;

        return function ($data, bool $setUndefined = true) use (&$setter) {
            foreach ($data as $property => $value) {
                $methodName = $setter($property);

                if ('' !== $methodName && method_exists($this, $methodName)) {
                    $this->{$methodName}($value);
                } elseif (true === $setUndefined || property_exists($this, $property)) {
                    $this->{$property} = $value;
                }
            }

            return $this;
        };
    }

    /**
     * @inheritDoc
     */
    protected function getExtractClosure(): Closure
    {
        if (null === $this->getterClosure) {
            $this->getterClosure = function (string $property) {
                if (!array_key_exists($property, $this->gettersNames)) {
                    $this->gettersNames[$property] = $this->getGetterName($property);
                }

                return $this->gettersNames[$property];
            };
        }

        $getter = $this->getterClosure;

        return function (array $properties) use (&$getter) {
            $result = [];

            if (count($properties) === 0) {
                $properties = array_keys(get_object_vars($this));
            }

            foreach ($properties as $property) {
                $methodName = $getter($property);

                if ('' !== $methodName && method_exists($this, $methodName)) {
                    $result[$property] = $this->{$methodName}();
                } else {
                    $result[$property] = property_exists($this, $property)
                        ? $this->{$property}
                        : null;
                }
            }

            return $result;
        };
    }

    /**
     * Prepare property name. Convert property name from snake to camel case
     *
     * @param string $propertyName
     * @return string
     */
    protected function prepareName(string $propertyName): string
    {
        return strpos($propertyName, '_') !== false
            ? implode('', array_map('ucfirst', explode('_', $propertyName)))
            : ucfirst($propertyName);
    }

    /**
     * Get setter method name
     * Return empty string to disable setters usage
     *
     * @param string $property
     * @return string
     */
    protected function getSetterName(string $property): string
    {
        return 'set' . $this->prepareName($property);
    }

    /**
     * Get getter method name
     * Return empty string to disable getters usage
     *
     * @param string $property
     * @return string
     */
    protected function getGetterName(string $property): string
    {
        return 'get' . $this->prepareName($property);
    }

}
