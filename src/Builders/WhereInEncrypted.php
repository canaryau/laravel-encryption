<?php

namespace Canaryau\LaravelEncryption\Builders;

use Canaryau\LaravelEncryption\Services\EncryptService;

trait WhereInEncrypted
{
    public function whereInEncrypted($column, $values = [])
    {
        $encryptedValues = [];

        foreach ($values as $value) {
            $encryptedValues[] = EncryptService::encrypt($value);
        }

        return self::whereIn($column, $encryptedValues);
    }

    public function orWhereInEncrypted($column, $values = [])
    {
        $encryptedValues = [];

        foreach ($values as $value) {
            $encryptedValues[] = EncryptService::encrypt($value);
        }

        return self::orWhereIn($column, $encryptedValues);
    }

    public function whereNotInEncrypted($column, $values = [])
    {
        $encryptedValues = [];

        foreach ($values as $value) {
            $encryptedValues[] = EncryptService::encrypt($value);
        }

        return self::whereNotIn($column, $encryptedValues);
    }

    public function orWhereNotInEncrypted($column, $values = [])
    {
        $encryptedValues = [];

        foreach ($values as $value) {
            $encryptedValues[] = EncryptService::encrypt($value);
        }

        return self::orWhereNotIn($column, $encryptedValues);
    }
}
