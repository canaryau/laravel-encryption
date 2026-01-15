<?php

namespace Canaryau\LaravelEncryption\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Canaryau\LaravelEncryption\LaravelEncryption
 */
class LaravelEncryptionFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \VendorName\Skeleton\Skeleton::class;
    }
}
