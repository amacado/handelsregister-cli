<?php

    namespace App\Exceptions;

    /**
     * // TODO support multiple search results (with pagination)
     */
    class MultipleSearchResultsUnsupportedException extends RenderableConsoleException
    {
        public function __construct(int $totalResults = null)
        {
            parent::__construct(
                sprintf('Found at least %s search results. Currently the CLI only supports single search results.',
                    $totalResults ?? 'more than one'
                ));
        }
    }
