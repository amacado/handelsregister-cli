<?php

    namespace App\Exceptions;

    use App\Enums\DownloadTypes;
    use App\Enums\Language;

    class UnknownDownloadTypeException extends RenderableConsoleException
    {
        public function __construct()
        {
            $options = DownloadTypes::toOptions()->keys()->collect()->join(', ');

            parent::__construct(sprintf('Unknown download type. Please choose one of %s.', $options));
        }
    }
