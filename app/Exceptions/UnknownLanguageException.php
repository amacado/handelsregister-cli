<?php

    namespace App\Exceptions;

    use App\Enums\Language;

    class UnknownLanguageException extends RenderableConsoleException
    {
        public function __construct()
        {
            $options = Language::toOptions()->keys()->collect()->join(', ');

            parent::__construct(sprintf('Unknown language for interaction. Please choose one of %s.', $options));
        }
    }
