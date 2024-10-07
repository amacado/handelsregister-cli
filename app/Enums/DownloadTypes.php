<?php

    namespace App\Enums;

    use Illuminate\Support\Str;

    /**
     * All downloadable types.
     */
    enum DownloadTypes: string
    {
        case CURRENT = 'AD';
        case CHRONOLOGICAL = 'CD';
        case HISTORICAL = 'HD';
        case STRUCTURED = 'SI';

        static function toOptions()
        {
            return collect(self::cases())->mapWithKeys(static fn(\UnitEnum $enum) => [$enum->value => Str::ucfirst(Str::lower($enum->name))]);
        }
    }
