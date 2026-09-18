<?php

namespace Canaryau\LaravelEncryption\Traits;

use Canaryau\LaravelEncryption\Builders\EloquentBuilder;
use Canaryau\LaravelEncryption\Services\EncryptService;
use Canaryau\LaravelEncryption\Support\HandleCastableAttributes;
use Canaryau\LaravelEncryption\Support\ParseAttributes;

trait EncryptsAttributes
{
    private $encryptionEnabled;

    public function __construct()
    {
        parent::__construct();

        $this->encryptionEnabled = config('laravel_encryption.enabled', true);
    }

    public function newEloquentBuilder($query)
    {
        return new EloquentBuilder($query);
    }

    public function getEncryptableAttributes()
    {
        return $this->encryptableAttributes ?? [];
    }

    public static function bootEncryptsAttributes()
    {
        static::saving(function ($model) {
            $model->encryptAttributes();
        });

        static::saved(function ($model) {
            $model->decryptAttributes();
        });

        static::retrieved(function ($model) {
            $model->decryptAttributes();
        });
    }

    public function encryptAttributes()
    {
        if (! $this->encryptionEnabled) {
            return;
        }

        foreach ($this->getEncryptableAttributes() as $attribute) {
            if (! empty($this->$attribute)) {
                $this->$attribute = EncryptService::encrypt(
                    ParseAttributes::parse(
                        $this->encryptableCasts ?? [],
                        $attribute,
                        $this->$attribute
                    )
                );
            }
        }
    }

    public function decryptAttributes()
    {
        if (! $this->encryptionEnabled) {
            return;
        }

        $casts = $this->encryptableCasts ?? [];

        foreach ($this->getEncryptableAttributes() as $attribute) {
            if (empty($this->$attribute)) {
                continue;
            }

            // Decrypt exactly once. Earlier versions first called
            // attributeIsEncrypted(), which decrypts the same value a second
            // time purely to see whether it throws; it never can, so every
            // encrypted column was being decrypted twice on hydration.
            $value = EncryptService::decrypt($this->$attribute);

            $this->$attribute = $casts === []
                ? $value
                : HandleCastableAttributes::handle($casts, $attribute, $value);
        }
    }

    /**
     * @deprecated openssl_decrypt() never throws, so this always returns true
     *             for any value and is no longer used by the trait itself. It
     *             is kept only so existing callers keep working.
     */
    public function attributeIsEncrypted($attribute)
    {
        try {
            EncryptService::decrypt($this->$attribute);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function parseAttribute($attribute)
    {
        $castType = $this->encryptableCasts[$attribute] ?? null;

        if ($this->$attribute instanceof \DateTime && $castType === 'datetime') {
            return $this->$attribute->format('Y-m-d H:i:s');
        }

        if ($this->$attribute instanceof \DateTime && $castType === 'date') {
            return $this->$attribute->format('Y-m-d');
        }

        if ($this->$attribute instanceof \DateTime && $castType === 'time') {
            return $this->$attribute->format('H:i:s');
        }

        if ($this->$attribute instanceof \DateTime) {
            return $this->$attribute->format('Y-m-d H:i:s');
        }

        if ($castType === 'json') {
            return json_encode($this->$attribute);
        }

        return $this->$attribute;
    }
}
