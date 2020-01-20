<?php

namespace Anonymous\Hydrator;


/**
 * Class Hydrator
 * @package Anonymous\Hydrator
 */
class Hydrator
{

    /**
     * Hydrate model with the provided data
     *
     * @param object $model             Model to hydrate
     * @param array|\Traversable $data  Data to fill model
     * @param bool $setUndefined        Set undefined properties
     * @return object                   $model
     */
    public function hydrate($model, $data, bool $setUndefined = false)
    {
        $closure = function ($data, \Closure $getSetterName, bool $setUndefined) {
            foreach ($data as $property => $value) {
                $setterName = $getSetterName($property);

                if ('' !== $setterName && method_exists($this, $setterName)) {
                    $this->{$setterName}($value);
                } elseif (property_exists($this, $property) || true === $setUndefined) {
                    $this->{$property} = $value;
                }
            }
        };

        $getSetterName = function ($property) {
            return $this->getSetterName($property);
        };

        $context = !$model instanceof \stdClass ? $model : null;
        $hydrator = $closure->bindTo($model, $context);

        $hydrator($data, $getSetterName, $setUndefined);

        return $model;
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
        $snakeConvert = static function ($matches) {
            return mb_convert_case($matches[1], MB_CASE_UPPER);
        };

        $camelName = preg_replace_callback('/_([a-z0-9])/i', $snakeConvert, $property);

        return 'set' . mb_convert_case(mb_substr($camelName, 0, 1), MB_CASE_UPPER) . mb_substr($camelName, 1);
    }

}
