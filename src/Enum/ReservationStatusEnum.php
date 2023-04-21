<?php

namespace App\Enum;

abstract class ReservationStatusEnum
{
    public const UNDER_RESERVATION = 'UNDER_RESERVATION';
    public const WAITING_FOR_PAYMENT = 'WAITING_FOR_PAYMENT';
    public const PAID_RESERVATION = 'PAID_RESERVATION';
    public const DELETED = 'DELETED';
    public const MANUAL_RESERVAITON = 'MANUAL_RESERVAITON';

    protected static $choices = [
        self::UNDER_RESERVATION => 'Foglalás alatt',
        self::WAITING_FOR_PAYMENT => 'Fizetésre vár',
        self::PAID_RESERVATION => 'Fizetett foglalás',
        self::DELETED => 'Törölt',
        self::MANUAL_RESERVAITON => 'Manuálisan foglalva'
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
            self::UNDER_RESERVATION,
            self::WAITING_FOR_PAYMENT,
            self::PAID_RESERVATION,
            self::DELETED,
            self::MANUAL_RESERVAITON
        ];
    }

    /**
     * @return array<string>
     */
    public static function getChoicesAdmin(): array
    {
        return [
            self::WAITING_FOR_PAYMENT,
            self::PAID_RESERVATION,
            self::DELETED,
            self::MANUAL_RESERVAITON
        ];
    }
}