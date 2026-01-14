<?php

namespace Canaryau\LaravelEncryption\Support;

class ParseAttributes
{
    public static function parse(array $encryptableCasts, string $attribute, $value)
    {
        $castType = $encryptableCasts[$attribute] ?? null;

        if ($value instanceof \DateTime && $castType === 'datetime') {
            return $value->format('Y-m-d H:i:s');
        }

        if ($value instanceof \DateTime && $castType === 'date') {
            return $value->format('Y-m-d');
        }

        if ($value instanceof \DateTime && $castType === 'time') {
            return $value->format('H:i:s');
        }

        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d H:i:s');
        }

        if ($castType === 'json') {
            return json_encode($value);
        }

        return $value;
    }
}
