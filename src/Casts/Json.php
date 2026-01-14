<?php

namespace Canaryau\LaravelEncryption\Casts;

class Json
{
    public static function handle($value)
    {
        return json_decode($value);
    }
}
