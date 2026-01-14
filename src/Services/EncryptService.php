<?php

namespace Canaryau\LaravelEncryption\Services;

class EncryptService
{
    public static function getKey()
    {
        return config('laravel_encryption.key', config('app.key'));
    }

    public static function getCipher()
    {
        $cipher = strtolower(config('laravel_encryption.cipher', 'AES-256-CBC'));

        if (! in_array($cipher, openssl_get_cipher_methods())) {
            throw new \Exception('The cipher method "' . $cipher . '" is not supported.');
        }

        return $cipher;
    }

    public static function encrypt($string): string
    {
        $iv = substr(md5(self::getKey()), 0, 16);

        $encrypted = openssl_encrypt($string, self::getCipher(), self::getKey(), 0, $iv);

        return base64_encode($encrypted);
    }

    public static function decrypt($encryptedString): string
    {
        $iv = substr(md5(self::getKey()), 0, 16);

        $decrypted = openssl_decrypt(base64_decode($encryptedString), self::getCipher(), self::getKey(), 0, $iv);

        return $decrypted;
    }
}
