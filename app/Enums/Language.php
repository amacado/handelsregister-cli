<?php

    namespace App\Enums;

    use Illuminate\Support\Str;

    enum Language: string
    {
        case GERMAN = 'DE';
        case ENGLISH = 'EN';
        case SPANISH = 'ES';
        case FRENCH = 'FR';
        case ITALIAN = 'IT';

        static function toOptions()
        {
            return collect(self::cases())->mapWithKeys(static fn(\UnitEnum $enum) => [$enum->value => Str::ucfirst(Str::lower($enum->name))]);
        }
    }
