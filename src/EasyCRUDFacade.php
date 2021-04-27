<?php

namespace Ebalo\EasyCRUD;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ebalo\EasyCRUD\Skeleton\SkeletonClass
 */
class EasyCRUDFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'easycrud';
    }
}
