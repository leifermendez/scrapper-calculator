<?php

namespace leifermendez\rbs_accommodations;

use Illuminate\Support\ServiceProvider;

class ProviderCalculator extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('Calculator', function () {
            return new CalculatorService();
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