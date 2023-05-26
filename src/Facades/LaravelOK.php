<?php

namespace Vormkracht10\LaravelOK\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Vormkracht10\LaravelOK\LaravelOK
 */
class LaravelOK extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Vormkracht10\LaravelOK\LaravelOK::class;
    }
}
