<?php

namespace App\Enum;

abstract class CaptainTypeEnum
{
    public const MANDATORY = 'MANDATORY';
    public const OPTIONAL_WITH_LICENCE = 'OPTIONAL_WITH_LICENCE';
    public const OPTIONAL = 'OPTIONAL';

    protected static $choices = [
        self::MANDATORY => 'Kötelező',
        self::OPTIONAL_WITH_LICENCE => 'Kisgéphajó vezetői engedély esetén opcionális',
        self::OPTIONAL => 'Opcionális'
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
            self::MANDATORY,
            self::OPTIONAL_WITH_LICENCE,
            self::OPTIONAL
        ];
    }
}