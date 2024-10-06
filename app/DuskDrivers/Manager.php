<?php

    namespace App\DuskDrivers;

    use NunoMaduro\LaravelConsoleDusk\ConsoleBrowserFactory;
    use NunoMaduro\LaravelConsoleDusk\Contracts\ConsoleBrowserFactoryContract;
    use NunoMaduro\LaravelConsoleDusk\Contracts\Drivers\DriverContract;
    use NunoMaduro\LaravelConsoleDusk\Contracts\ManagerContract;
    use NunoMaduro\LaravelConsoleDusk\Manager as BaseManager;

    class Manager extends BaseManager implements ManagerContract
    {
        public function __construct(DriverContract $driver = null, ConsoleBrowserFactoryContract $browserFactory = null)
        {

            parent::__construct($driver, $browserFactory);

            $this->driver         = $driver ?: new ChromeDriver();
            $this->browserFactory = $browserFactory ?: new ConsoleBrowserFactory();
        }
    }
