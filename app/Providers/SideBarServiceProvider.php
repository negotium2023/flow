<?php

namespace App\Providers;

use App\Step;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Process;
use App\Config;

class SideBarServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $sidebar_process = [];
        $sidebar_process_detail = [];
        $sidebar_process_statuses = [];


        if (Schema::hasTable('steps')) {

            $sidebar_process_statuses = Process::orderBy('id')->where('process_type_id', '=', 1)->where('form',0)->orderBy('name')->get();
            $default_process = Config::first()->default_onboarding_process;

            foreach($sidebar_process_statuses as $sidebar_process_status){
                $sidebar_process_detail = [
                    "process_type_id" => $sidebar_process_status->process_type_id,
                    "id" => $sidebar_process_status->id,
                    "name" => $sidebar_process_status->name,
                    "steps" => Step::where('process_id',$sidebar_process_status->id)->orderBy('order')->pluck('name', 'id')->prepend('All','all'),
                    "default" => ($default_process == $sidebar_process_status->id ? 1 : 0)
                ];
                array_push($sidebar_process, $sidebar_process_detail);
            }

        }

        view()->composer('flow.sidebar', function ($view) use ($sidebar_process) {
            $view->with('sidebar_process_statuses', $sidebar_process);
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