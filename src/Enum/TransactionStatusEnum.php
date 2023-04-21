<?php

namespace App\Enum;

abstract class TransactionStatusEnum
{
    public const UNPAID = 'UNPAID';
    public const INPROGRESS = 'INPROGRESS';
    public const FAILED = 'FAILED';
    public const IPNCHECKED = 'IPNCHECKED';

    protected static $choices = [
        self::UNPAID => 'Fizetetlen',
        self::INPROGRESS => 'Fizetés folyamatban',
        self::FAILED => 'Sikertelen fizetés',
        self::IPNCHECKED => 'Sikeres IPN vizsgálat'
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
            self::UNPAID,
            self::INPROGRESS,
            self::FAILED,
            self::IPNCHECKED
        ];
    }
}