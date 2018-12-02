<?php
namespace Bytepath\Pendulum;

use Illuminate\Log\Logger;
use Illuminate\Session\SessionManager;
use Illuminate\Support\ServiceProvider;

class PendulumServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . "/Views", "pendulum");
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(){}
}
