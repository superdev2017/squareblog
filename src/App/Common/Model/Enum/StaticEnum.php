<?php

namespace App\Common\Model\Enum;

class StaticEnum
{
    static protected $enum = array();

    public static function toArray()
    {
        return static::$enum;
    }

    public static function getLabel($key)
    {
        static::checkKey($key);

        return static::$enum[$key];
    }

    public static function getKey($label)
    {
        return array_search($label, static::$enum);
    }

    public static function getKeys()
    {
        return array_keys(static::$enum);
    }

    public static function hasKey($key)
    {
        return isset(static::$enum[$key]);
    }

    public static function checkKey($key)
    {
        if (!static::hasKey($key)) {
            throw new \InvalidArgumentException(sprintf('bad key "%s"', $key));
        }
    }
}
