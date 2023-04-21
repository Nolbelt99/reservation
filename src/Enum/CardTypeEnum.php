<?php

namespace App\Enum;

abstract class CardTypeEnum
{
    public const PASSPORT = 'PASSPORT';
    public const ID_CARD = 'ID_CARD';
    public const DRIVER_LICENSE = 'DRIVER_LICENSE';

    protected static $choices = [
        self::PASSPORT => 'útlevél',
        self::ID_CARD => 'személyi igazolvány',
        self::DRIVER_LICENSE => 'jogosítvány'
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
            self::PASSPORT,
            self::ID_CARD,
            self::DRIVER_LICENSE
        ];
    }
}