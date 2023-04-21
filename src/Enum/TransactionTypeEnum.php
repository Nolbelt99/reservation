<?php

namespace App\Enum;

abstract class TransactionTypeEnum
{
    public const LOCK = 'LOCK';
    public const PAYMENT = 'PAYMENT';

    protected static $choices = [
        self::LOCK => 'Zárolás',
        self::PAYMENT => 'Kifizetés'
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
            self::LOCK,
            self::PAYMENT
        ];
    }
}