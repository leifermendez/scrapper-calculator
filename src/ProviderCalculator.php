<?php

namespace leifermendez\rbs_accommodations;

use Illuminate\Support\ServiceProvider;

class ProviderCalculator extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('Calculator', function () {

            $credentials = array(
                'DB_PORT' => env('DB_PORT'),
                'DB_HOST' => env('DB_HOST'),
                'DB_DATABASE' => env('DB_DATABASE'),
                'DB_USERNAME' => env('DB_USERNAME'),
                'DB_PASSWORD' => env('DB_PASSWORD'),
                'DB_TABLE_SCRAPPER' => env('DB_TABLE_SCRAPPER'),
            );
            return new CalculatorService($credentials);
        }
        );
    }

    /**
     * @return array
     */
    public function provides()
    {
        return array('Calculator');
    }
}