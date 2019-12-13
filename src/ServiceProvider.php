<?php

namespace LeMaX10\WaitingJob;

use Illuminate\Foundation\Application as LaravelApplication;
use Laravel\Lumen\Application as LumenApplication;
use LeMaX10\WaitingJob\WaitingQueueManager;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class AppServiceProvider
 * @package App\Providers
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(WaitingQueueManager::class, static function() {
            return new WaitingQueueManager(config('waitingjob'));
        });
    }

    /**
     *
     */
    public function boot()
    {
        $source = realpath($raw = __DIR__ . '/../config/waitingjob.php') ?: $raw;

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('waitingjob.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('waitingjob');
        }

        $this->mergeConfigFrom($source, 'waitingjob');
    }
}
