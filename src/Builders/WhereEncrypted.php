<?php

namespace Canaryau\LaravelEncryption\Builders;

use Canaryau\LaravelEncryption\Services\EncryptService;

trait WhereEncrypted
{
    public function whereEncrypted($column, $value = null, $operator = '=')
    {
        $encryptedValue = EncryptService::encrypt($value);

        return self::where($column, $operator, $encryptedValue);
    }

    public function orWhereEncrypted($column, $value = null, $operator = '=')
    {
        $encryptedValue = EncryptService::encrypt($value);

        return self::orWhere($column, $operator, $encryptedValue);
    }

    public function whereLikeEncrypted($column, $value = null)
    {
        $encryptedValue = EncryptService::encrypt($value);

        return self::whereLike($column, $encryptedValue);
    }
}
