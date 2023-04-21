<?php

namespace App\Enum;

abstract class ServiceTypeEnum
{
    public const APARTMENT = 'APARTMENT';
    public const SHIP = 'SHIP';
    public const EBIKE = 'EBIKE';

    protected static $choices = [
        self::APARTMENT => 'Apartman',
        self::SHIP => 'Hajó',
        self::EBIKE => 'Elektoromos kerékpár'
    ];

    public static function getName($name)
    {
        if (!isset(static::$choices[$name])) {
            return "($name)";
        }

        return static::$choices[$name];
    }

    /**
     * @return array<string>
     */
    public static function getChoices(): array
    {
        return [
            self::APARTMENT,
            self::SHIP,
            self::EBIKE
        ];
    }
}