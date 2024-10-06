<?php

    namespace App\Providers;

    use App\DuskDrivers\Manager;
    use Facebook\WebDriver\Remote\RemoteWebElement;
    use Illuminate\Support\ServiceProvider;
    use Illuminate\Support\Str;
    use Laravel\Dusk\Browser;
    use Laravel\Dusk\ElementResolver;
    use NunoMaduro\LaravelConsoleDusk\Contracts\ManagerContract;

    class AppServiceProvider extends ServiceProvider
    {
        /**
         * Bootstrap any application services.
         */
        public function boot(): void
        {
            $this->app->bind(ManagerContract::class, static fn() => new Manager());

            // TODO
            // Credit to https://gist.github.com/enes1004/832255ba912d94baac77878deeb4fdfa
            ElementResolver::macro("findBySelectorAndText", function (string $selector, string $text): array {
                $elements = [];
                // imitates ElementResolver::findButtonByText
                foreach ($this->all($selector) as $element) {
                    if (Str::contains($element->getText(), $text)) {
                        $elements[] = $element;
                    }
                }

                return $elements;
            });

            // TODO
            // Credit to https://gist.github.com/enes1004/832255ba912d94baac77878deeb4fdfa
            // imitates Concerns\InteractsWithMouse::click
            Browser::macro("clickElementWithText", function (string $selector, string $text): Browser {
                /** @var RemoteWebElement[] $elements */
                $elements = $this->resolver->findBySelectorAndText($selector, $text);
                foreach ($elements as $one) {
                    $one->click();
                }

                return $this;
            });
        }
    }
