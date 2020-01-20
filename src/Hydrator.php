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
    public static function hydrate($model, $data, bool $setUndefined = false)
    {
        return static::applyClosure($model, function ($data, \Closure $getSetterName, bool $setUndefined) {
            foreach ($data as $property => $value) {
                $setterName = $getSetterName($property);

                if ('' !== $setterName && method_exists($this, $setterName)) {
                    $this->{$setterName}($value);
                } elseif (property_exists($this, $property) || true === $setUndefined) {
                    $this->{$property} = $value;
                }
            }

            return $this;
        }, $data, static function ($property) {
            return static::getSetterName($property);
        }, $setUndefined);
    }

    /**
     * Get model properties as array
     *
     * @param $model
     * @param array $properties
     * @return array
     */
    public static function toArray($model, array $properties = []): array
    {
        return static::applyClosure($model, function ($properties, \Closure $getGetterName) {
            $result = [];

            if (count($properties) === 0) {
                $properties = array_keys(get_object_vars($this));
            }

            foreach ($properties as $property) {
                $getterName = $getGetterName($property);

                if ('' !== $getterName && method_exists($this, $getterName)) {
                    $result[$property] = $this->{$getterName}();
                } else {
                    $result[$property] = property_exists($this, $property)
                        ? $this->{$property}
                        : null;
                }
            }

            return $result;
        }, $properties, static function ($property) {
            return static::getGetterName($property);
        });
    }

    /**
     * Bind and apply closure to the model
     *
     * @param $model
     * @param \Closure $closure
     * @param mixed ...$params
     * @return mixed
     */
    protected static function applyClosure($model, \Closure $closure, ...$params)
    {
        $context = !$model instanceof \stdClass ? $model : null;
        $bindedClosure = $closure->bindTo($model, $context);

        return $bindedClosure(...$params);
    }

    /**
     * Prepare property name. Convert property name from snake to camel case
     *
     * @param string $snakeCaseName
     * @return string
     */
    protected static function prepareName(string $snakeCaseName): string
    {
        $snakeConvert = static function ($matches) {
            return mb_convert_case($matches[1], MB_CASE_UPPER);
        };

        $camelName = preg_replace_callback('/_([a-z0-9])/i', $snakeConvert, $snakeCaseName);

        return mb_convert_case(mb_substr($camelName, 0, 1), MB_CASE_UPPER) . mb_substr($camelName, 1);
    }

    /**
     * Get setter method name
     * Return empty string to disable setters usage
     *
     * @param string $property
     * @return string
     */
    protected static function getSetterName(string $property): string
    {
        return 'set' . static::prepareName($property);
    }

    /**
     * Get getter method name
     * Return empty string to disable getters usage
     *
     * @param string $property
     * @return string
     */
    protected static function getGetterName(string $property): string
    {
        return 'get' . static::prepareName($property);
    }

}
