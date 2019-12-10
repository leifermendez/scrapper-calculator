<?php

namespace leifermendez\rbs_accommodations;

use Illuminate\Support\Facades\Facade;

class CalculatorFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Calculator';
    }
}