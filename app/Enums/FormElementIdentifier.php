<?php

    namespace App\Enums;

    enum FormElementIdentifier: string
    {
        case REGISTER_TYPE = 'registerArt';
        case REGISTER_COURT = 'registergericht';
        case LEGAL_STATUS = 'rechtsform';
        case COUNTRY = 'staat';
        case KEYWORDS_MATCH_SIMILAR = 'aenlichLautendeSchlagwoerterBoolChkbox';
        case INCLUDE_DELETED_COMPANIES = 'auchGeloeschte';
        case ONLY_BRANCHES_ACCORDING_TO_NEW_LAW = 'nurZweigniederlassungenBoolChkbox';
    }
