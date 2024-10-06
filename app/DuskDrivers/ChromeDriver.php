<?php

    namespace App\DuskDrivers;

    use Facebook\WebDriver\Chrome\ChromeOptions;
    use Facebook\WebDriver\Remote\DesiredCapabilities;
    use Facebook\WebDriver\Remote\RemoteWebDriver;
    use Laravel\Dusk\Chrome\ChromeProcess;
    use NunoMaduro\LaravelConsoleDusk\Drivers\Chrome;

    class ChromeDriver extends Chrome
    {
        public function getDriver()
        {
            $options = new ChromeOptions();
            $options->addArguments(
                [
                    '--no-sandbox',
                    '--disable-gpu',
                    '--headless',
                    '--window-size=1920,1080',
                    '--port=9515',
                    '--whitelisted-ips',
                    '--disable-dev-shm-usage'
                ]
            );

            // Allow downloading files from `Handelsregister` within CLI build
            $options->setExperimentalOption('prefs', [
                'download.default_directory' => storage_path('laravel-console-dusk/downloads')
            ]);


            return RemoteWebDriver::create(
                'http://localhost:9515',
                DesiredCapabilities::chrome()
                                   ->setCapability(
                                       ChromeOptions::CAPABILITY,
                                       $options
                                   )
            );
        }

        public function open(): void
        {
            static::useChromedriver('/app/vendor/laravel/dusk/bin/chromedriver-linux');
            parent::open();
        }

        protected static function buildChromeProcess(array $arguments = [])
        {
            $arguments = ['--headless', '--whitelisted-ips', '--port=9515', '--no-sandbox', '--window-size=1920,1080', '--disable-gpu'];

            return (new ChromeProcess(static::$chromeDriver))->toProcess($arguments);
        }
    }
