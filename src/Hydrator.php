<?php

namespace Anonymous\Hydrator;


use Closure;

/**
 * Class Hydrator
 * @package Anonymous\Hydrator
 */
class Hydrator
{

    /**
     * @var Closure
     */
    protected $hydrateClosure;

    /**
     * @var Closure
     */
    protected $extractClosure;

    /**
     * @var array
     */
    protected $properties = [];


    /**
     * Hydrate model with the provided data
     *
     * @param object $model             Model to hydrate
     * @param array|\Traversable $data  Data to fill model
     * @param bool $setUndefined        Set undefined properties
     * @return object                   $model
     */
    public function hydrate($model, $data, bool $setUndefined = true)
    {
        if (null === $this->hydrateClosure) {
            $this->hydrateClosure = $this->getHydrateClosure();
        }

        $context = !$model instanceof \stdClass ? $model : null;
        $bindedClosure = $this->hydrateClosure->bindTo($model, $context);

        return $bindedClosure($data, $setUndefined);
    }

    /**
     * Get model properties as array
     *
     * @param $model
     * @param array $properties
     * @return array
     */
    public function extract($model, array $properties = []): array
    {
        if (null === $this->extractClosure) {
            $this->extractClosure = $this->getextractClosure();
        }

        $context = !$model instanceof \stdClass ? $model : null;
        $bindedClosure = $this->extractClosure->bindTo($model, $context);

        return $bindedClosure($properties);
    }

    /**
     * Hydrate under the hood
     *
     * @return Closure
     */
    protected function getHydrateClosure(): Closure
    {
        return function ($data, bool $setUndefined = true) {
            foreach ($data as $property => $value) {
                if (true === $setUndefined || property_exists($this, $property)) {
                    $this->{$property} = $value;
                }
            }

            return $this;
        };
    }

    /**
     * To array under the hood
     *
     * @return Closure
     */
    protected function getextractClosure(): Closure
    {
        return function (array $properties) {
            $result = [];

            $isPropertiesCustom = count($properties) > 0;

            if (false === $isPropertiesCustom) {
                $properties = array_keys(get_object_vars($this));
            }

            foreach ($properties as $property) {
                $result[$property] = false === $isPropertiesCustom || property_exists($this, $property)
                    ? $this->{$property}
                    : null;
            }

            return $result;
        };
    }

}
