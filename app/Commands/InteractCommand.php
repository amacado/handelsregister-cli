<?php

    namespace App\Commands;

    use App\Contracts\RenderableExceptionContract;
    use App\Enums\FederalState;
    use App\Enums\FormElementIdentifier;
    use App\Enums\FormElementKeywordOption;
    use App\Enums\Language;
    use App\Exceptions\MultipleSearchResultsUnsupportedException;
    use App\Exceptions\NoSearchResultsException;
    use App\Exceptions\RenderableConsoleException;
    use App\Exceptions\UnknownLanguageException;
    use Facebook\WebDriver\Exception\TimeoutException;
    use Facebook\WebDriver\Remote\RemoteWebElement;
    use Illuminate\Console\Concerns\PromptsForMissingInput;
    use Illuminate\Support\Collection;
    use Illuminate\Support\Str;
    use Laravel\Dusk\Browser;
    use LaravelZero\Framework\Commands\Command;
    use NunoMaduro\LaravelConsoleDusk\ConsoleBrowser;
    use function Laravel\Prompts\select;

    class InteractCommand extends Command implements \Illuminate\Contracts\Console\PromptsForMissingInput
    {
        use PromptsForMissingInput;

        /**
         * The name and signature of the console command.
         *
         * @var string
         */
        protected $signature = 'handelsregister-cli
                                    {language=DE : Language which is used for interaction with `Handelsregister`.}
                                    
                                    {--state=* : Select zero, one or more federal states to search in. If none are passed, the search will not be limited to a specific state. Valid codes are based on iso:code:3166:DE.}
                                    
                                    {--keywords= : Search for company or keywords.}
                                    {--keywords-option=any : Select an option how `keywords` are treated; One of (all|any|exact).}
                                    {--keywords-match-similar : If flag is set, similar matching keywords will be included in results.}
                                    
                                    {--subsidiary-office= : Subsidiary / registered office.}
                                    
                                    {--include-deleted : If flag is set, search results will also include deleted companies.}
                                    {--only-branches : If flag is set, search only for branches in accordance with new law.}
                                    
                                    {--register-type= : Type of register.}
                                    {--register-number= : Company register number.}
                                    {--register-court= : Register court.}
                                    {--legal-status= : Company legal status (input value is language-dependent).}
                                    {--country= : Country where the company is located (input value is language-dependent).}
                                    {--postal-code= : Postal code where the company is located.}
                                    {--location= : Location/City where the company is located.}
                                    {--street= : Street where the company is located.}';
        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = 'Command line interaction with the `Handelsregister` advanced search form.';

        protected function promptForMissingArgumentsUsing(): array
        {
            return [
                'language' => static fn() => select('Please select the language for interaction with the `Handelsregister`.', Language::toOptions())
            ];
        }

        /**
         * Execute the console command.
         */
        public function handle()
        {
            try {
                $language = Language::tryFrom(Str::upper($this->argument('language')));
                throw_if($language === null, UnknownLanguageException::class);

                $this->browse(function (ConsoleBrowser $browser) use ($language) {
                    /** @var \Laravel\Dusk\Browser $browser */
                    $browser = $browser;

                    $browser
                        ->visit(config('app.api.url'))
                        ->waitFor('form#form') // wait until advanced search form is loaded
                        ->tap(self::switchLanguage($language))

                        // primary search parameters
                        ->tap(self::handleFederalStates(collect($this->option('state'))->map(static fn($state) => FederalState::tryFromName($state))->filter()))
                        ->type('textarea#form\:schlagwoerter', $this->option('keywords'))
                        ->tap(self::selectKeywordsOption(FormElementKeywordOption::tryFrom(Str::lower($this->option('keywords-option')))))
                        ->tap(self::handleCheckbox(FormElementIdentifier::KEYWORDS_MATCH_SIMILAR, $this->option('keywords-match-similar')))
                        ->type('input#form\:NiederlassungSitz', $this->option('subsidiary-office'))

                        // additional search options
                        ->tap(self::handleCheckbox(FormElementIdentifier::INCLUDE_DELETED_COMPANIES, $this->option('include-deleted')))
                        ->tap(self::handleCheckbox(FormElementIdentifier::ONLY_BRANCHES_ACCORDING_TO_NEW_LAW, $this->option('only-branches')))

                        // information on the main office (detailed search parameters)
                        ->tap(self::selectElement(FormElementIdentifier::REGISTER_TYPE, $this->option('register-type')))
                        ->type('input#form\:registerNummer', $this->option('register-number'))
                        ->tap(self::selectElement(FormElementIdentifier::REGISTER_COURT, $this->option('register-court')))
                        ->tap(self::selectElement(FormElementIdentifier::LEGAL_STATUS, $this->option('legal-status')))
                        ->tap(self::selectElement(FormElementIdentifier::COUNTRY, $this->option('country')))
                        ->type('input#form\:postleitzahl', $this->option('postal-code'))
                        ->type('input#form\:ort', $this->option('location'))
                        ->type('input#form\:strasse', $this->option('street'))

                        // optional
                        ->pause(1000)
                        ->tap(self::fullPageScreenshot('before'))

                        // submit
                        ->scrollTo('button#form\:btnSuche')
                        ->press('form\:btnSuche')
                        ->pause(5000)
                        ->tap(self::handleSubmissionErrors())

                        // handle search results
                        ->waitFor('form#ergebnissForm', 30)
                        ->tap(self::fullPageScreenshot('result'))
                        ->with('tbody#ergebnissForm\:selectedSuchErgebnisFormTable_data', static function (Browser $browser) {
                            $resultRows   = collect($browser->elements('tr[data-ri]'));
                            $countResults = $resultRows->count();

                            throw_if($countResults > 1, MultipleSearchResultsUnsupportedException::class, $countResults);
                            throw_if($countResults === 0, NoSearchResultsException::class);

                            $browser->with('tr[data-ri="0"]', static function (Browser $browser) {
                                // TODO implement search result handling..

                                // TODO for any reason currently only one download works at a time..
                                $browser
                                    ->clickLink('AD')
                                    ->pause(5000)
                                    ->screenshot('download');
                            });
                        }


                        );
                });
            } catch (RenderableExceptionContract $exception) {
                $exception->render();
            } catch (TimeoutException $timeoutException) {
                // TODO maybe catch all throwables?
                $exception = new RenderableConsoleException($timeoutException->getMessage(), previous: $timeoutException);
                $exception->render();
            }
        }

        /**
         * Parse form submission for error messages and throw an exception
         * if any errors occurred.
         *
         * @return \Closure
         */
        protected static function handleSubmissionErrors(): \Closure
        {
            return static function (Browser $browser): void {
                $errors = collect($browser->elements('.ui-messages-error'))
                    ->map(static fn(RemoteWebElement $webElement) => $webElement->getText());

                throw_if($errors->count() > 0, new RenderableConsoleException($errors));
            };
        }

        /**
         * Switch language of the `Handelsregister` website. Some input parameters
         * are language dependent, therefore when the language is changed
         * you might have to adjust your input parameters.
         *
         * @param \App\Enums\Language $language
         *
         * @return \Closure
         */
        protected static function switchLanguage(Language $language): \Closure
        {
            return static function (Browser $browser) use ($language): void {
                $langElement     = $browser->text('li#localSubMenu a span.ui-menuitem-text');
                $currentLanguage = Language::tryFrom($langElement);

                if ($currentLanguage === $language) {
                    return;
                }

                $browser->click('li#localSubMenu')
                        ->waitForTextIn('li#localSubMenu ul.ui-menu-list', $language->value)
                        ->clickElementWithText('li#localSubMenu ul.ui-menu-list li', $language->value)
                        ->waitForTextIn('li#localSubMenu a span.ui-menuitem-text', $language->value)
                        ->pause(1000);
            };
        }

        /**
         * Take a full-page screenshot of the current page.
         *
         * @param string $filename
         *
         * @return \Closure
         */
        protected static function fullPageScreenshot(string $filename): \Closure
        {
            return static fn(Browser $browser) => $browser
                ->resize(1920, 4000)
                ->screenshotElement('div#page-wrapper', $filename)
                ->resize(1920, 1080);
        }

        /**
         * Handle checkboxes selecting the requested federal states.
         *
         * @param \Illuminate\Support\Collection<FederalState> $federalStates Collection of federal states to be enabled.
         *
         * @return \Closure
         */
        protected static function handleFederalStates(Collection $federalStates): \Closure
        {
            return static fn(Browser $browser) => when($federalStates->count() > 0,
                static fn() => $federalStates->each(static fn(FederalState $state) => $browser->click('div#form\:' . $state->toElement() . ' div.ui-chkbox-box')
                )
            );
        }

        /**
         * (Un-)Check a checkbox with given element identifier.
         *
         * @param \App\Enums\FormElementIdentifier $formElementIdentifier
         * @param bool                             $check If true is given the checkbox will be selected.
         *
         * @return \Closure
         */
        protected static function handleCheckbox(FormElementIdentifier $formElementIdentifier, bool $check): \Closure
        {
            return static fn(Browser $browser) => when($check,
                static fn() => $browser->click('div#form\:' . $formElementIdentifier->value . ' div.ui-chkbox-box')
            );
        }

        /**
         * Select the input value for the keywords option input parameter.
         *
         * @param \App\Enums\FormElementKeywordOption|null $keywordOption
         *
         * @return \Closure
         */
        protected static function selectKeywordsOption(FormElementKeywordOption|null $keywordOption): \Closure
        {
            return static fn(Browser $browser) => when($keywordOption !== null,
                static fn() => $browser->click('label[for="' . $keywordOption->toElement() . '"]')
            );
        }

        /**
         * Select an option in given select input. The selected option
         * is identified by the option text.
         *
         * @param \App\Enums\FormElementIdentifier $elementIdentifier Identifier of the select input element
         * @param string|null                      $optionText        Selected option based on the text (not value); When null is given no option will be selected and the input remains in default state
         *
         * @return \Closure
         */
        protected static function selectElement(FormElementIdentifier $elementIdentifier, string|null $optionText): \Closure
        {
            return static fn(Browser $browser) => when($optionText, static fn() => $browser
                ->click('div#form\:' . $elementIdentifier->value)
                ->with('div#form\:' . $elementIdentifier->value . '_panel',
                    static fn(Browser $panel) => $panel
                        ->waitForTextIn('ul#form\:' . $elementIdentifier->value . '_items', $optionText)
                        ->clickElementWithText('li', $optionText)
                )
            );
        }
    }
