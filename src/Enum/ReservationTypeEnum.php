<?php

namespace App\Enum;

abstract class ReservationTypeEnum
{
    public const INDEPENDENT = 'INDEPENDENT';
    public const NOT_INDEPENDENT = 'NOT_INDEPENDENT';
    public const CANT_BOOK = 'CANT_BOOK';

    protected static $choices = [
        self::INDEPENDENT => 'Önállóan foglalható',
        self::NOT_INDEPENDENT => 'Önállóan nem foglalható',
        self::CANT_BOOK => 'Nem foglalható'
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
            self::INDEPENDENT,
            self::NOT_INDEPENDENT,
            self::CANT_BOOK
        ];
    }
}