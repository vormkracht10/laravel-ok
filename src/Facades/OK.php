<?php

namespace Backstage\Laravel\OK\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Backstage\LaravelOK\OK
 */
class OK extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Backstage\Laravel\OK\OK::class;
    }
}
