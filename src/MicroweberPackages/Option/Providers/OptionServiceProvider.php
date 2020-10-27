<?php
/*
 * This file is part of the Microweber framework.
 *
 * (c) Microweber CMS LTD
 *
 * For full license information see
 * https://github.com/microweber/microweber/blob/master/LICENSE
 *
 */

namespace MicroweberPackages\Option\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use MicroweberPackages\Option\Facades\Option;
use MicroweberPackages\Option\Models\Option as OptionModel;
use MicroweberPackages\Option\OptionManager;


class OptionServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('option_manager', function ($app) {
            return new OptionManager();
        });

        $this->app->bind('option',function(){
            return new OptionModel();
        });
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * @property \MicroweberPackages\Option\OptionManager    $option_manager
         */

        $this->loadMigrationsFrom(__DIR__ . '/../migrations/');

        $aliasLoader = AliasLoader::getInstance();
        $aliasLoader->alias('Option', Option::class);

    }
}