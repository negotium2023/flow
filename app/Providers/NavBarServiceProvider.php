<?php

namespace App\Providers;

use App\Config;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class NavBarServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $navbar_config_enable_support = false;

        if (Schema::hasTable('configs')) {
            if (Cache::has('navbar_config_enable_support')) {
                $navbar_config_enable_support = Cache::get('navbar_config_enable_support');
            } else {
                $navbar_config_enable_support = Config::first()->enable_support;
                Cache::add('navbar_config_enable_support', $navbar_config_enable_support, 5);
            }
        }

        view()->composer('flow.header', function ($view) use ($navbar_config_enable_support) {
            $view->with('navbar_config_enable_support', $navbar_config_enable_support);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
