<?php

    namespace App\Exceptions;

    class NoSearchResultsException extends RenderableConsoleException
    {
        public function __construct()
        {
            parent::__construct('No search results for given search parameters found.');
        }
    }
