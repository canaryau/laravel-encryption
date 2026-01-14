<?php

namespace Canaryau\LaravelEncryption\Casts;

class DateTime
{
    public static function handle($value)
    {
        if (class_exists('Carbon\Carbon')) {
            return \Carbon\Carbon::parse($value);
        }

        return new \DateTime($value);
    }
}
