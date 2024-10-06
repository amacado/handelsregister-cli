<?php

    namespace App\Enums;

    use App\Exceptions\RenderableConsoleException;

    enum FormElementKeywordOption: string
    {
        case CONTAIN_ALL_KEYWORDS = 'all';
        case CONTAIN_AT_LEAST_ONE_KEYWORD = 'any';
        case CONTAIN_EXACT_COMPANY_NAME = 'exact';

        /**
         * @throws \App\Exceptions\RenderableConsoleException
         */
        public function toElement(): string
        {
            return match ($this) {
                FormElementKeywordOption::CONTAIN_ALL_KEYWORDS => 'form\:schlagwortOptionen\:0',
                FormElementKeywordOption::CONTAIN_AT_LEAST_ONE_KEYWORD => 'form\:schlagwortOptionen\:1',
                FormElementKeywordOption::CONTAIN_EXACT_COMPANY_NAME => 'form\:schlagwortOptionen\:2',
                default => throw new RenderableConsoleException('Not implemented keyword option.')
            };
        }
    }
