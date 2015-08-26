<?php


namespace kriskbx\mikado\Facades;

use Illuminate\Support\Facades\Facade;

class Mikado extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'kriskbx\mikado\Mikado';
    }
}