<?php

    namespace App\Enums;

    use Illuminate\Support\Str;

    /**
     * https://www.iso.org/obp/ui/#iso:code:3166:DE
     */
    enum FederalState: string
    {
        case BW = 'BW';
        case BY = 'BY';
        case BE = 'BE';
        case BB = 'BB';
        case HB = 'HB';
        case HH = 'HH';
        case HE = 'HE';
        case MV = 'MV';
        case NI = 'NI';
        case NW = 'NW';
        case RP = 'RP';
        case SL = 'SL';
        case SN = 'SN';
        case ST = 'ST';
        case SH = 'SH';
        case TH = 'TH';

        public static function fromName(string $name): FederalState
        {
            $name = Str::remove(['DE_', 'DE-'], $name);

            foreach (self::cases() as $states) {
                if ($name === $states->name) {
                    return $states;
                }
            }

            throw new \ValueError("$name is not a valid backing value for enum " . static::class);
        }

        public static function tryFromName(string $name): FederalState|null
        {
            try {
                return self::fromName($name);
            } catch (\ValueError $error) {
                return null;
            }
        }

        public function toElement()
        {
            return match ($this) {
                FederalState::BW => 'Baden-Württemberg',
                FederalState::BY => 'Bayern',
                FederalState::BE => 'Berlin',
                FederalState::BB => 'Brandenburg',
                FederalState::HB => 'Bremen',
                FederalState::HH => 'Hamburg',
                FederalState::HE => 'Hessen',
                FederalState::MV => 'Mecklenburg-Vorpommern',
                FederalState::NI => 'Niedersachsen',
                FederalState::NW => 'Nordrhein-Westfalen',
                FederalState::RP => 'Rheinland-Pfalz',
                FederalState::SL => 'Saarland',
                FederalState::SN => 'Sachsen',
                FederalState::ST => 'Sachsen-Anhalt',
                FederalState::SH => 'Schleswig-Holstein',
                FederalState::TH => 'Thüringen',
            };
        }
    }
