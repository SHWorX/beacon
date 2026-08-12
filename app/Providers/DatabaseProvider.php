<?php
/*
 * Project:     Beacon
 * File:        DatabaseProvider.php
 * Date:        2026-06-10
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Providers;

use App\Database\MigrationRepository;
use Illuminate\Container\Container as IlluminateContainer;
use Illuminate\Events\Dispatcher;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Connection;

final readonly class DatabaseProvider extends ServiceProvider
{

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->container->singleton(
            Manager::class,
            function () {
                $capsule = new Manager();

                $connection = [
                    'driver'   => config('database.driver'),
                    'host'     => config('database.host'),
                    'port'     => config('database.port'),
                    'database' => config('database.database'),
                    'username' => config('database.username'),
                    'password' => config('database.password'),
                    'charset'  => config('database.charset'),
                    'prefix'   => config('database.prefix', ''),
                ];

                if (config('database.driver') === 'mysql') {
                    $connection['collation'] = config('database.collation');
                }

                $capsule->addConnection($connection);
                $capsule->setEventDispatcher(new Dispatcher(new IlluminateContainer()));
                $capsule->setAsGlobal();
                $capsule->bootEloquent();

                return $capsule;
            }
        );

        $this->container->singleton(
            Connection::class,
            fn () => $this->container
                ->make(Manager::class)
                ->getConnection()
        );

        $this->container->singleton(
            MigrationRepository::class,
            fn () => new MigrationRepository($this->container->make(Connection::class))
        );
    }

    public function boot(): void
    {
        $this->container->make(Manager::class);
    }
}