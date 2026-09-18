<?php

namespace Canaryau\LaravelEncryption\Services;

class EncryptService
{
    /**
     * The last cipher name that passed validation against
     * openssl_get_cipher_methods(). Validating is comparatively expensive
     * (the list holds 100+ names and is rebuilt on every call) and the
     * configured cipher does not change within a process, so it is checked
     * once and then trusted until the configured value changes.
     */
    private static ?string $validatedCipher = null;

    /**
     * The key the cached IV was derived from, and that IV. The IV is a fixed
     * derivation of the key, so it only needs recomputing when the configured
     * key changes.
     */
    private static ?string $ivKey = null;

    private static ?string $iv = null;

    /**
     * The configured key, falling back to the app key when none is set. The
     * published config ships `'key' => env('LARAVEL_ENCRYPTION_KEY', null)`,
     * and config()'s second argument only applies when the key is ABSENT, so
     * a present-but-null value must be treated as unset too.
     */
    public static function getKey()
    {
        return config('laravel_encryption.key') ?? config('app.key');
    }

    public static function getCipher()
    {
        $cipher = strtolower(config('laravel_encryption.cipher', 'AES-256-CBC'));

        if ($cipher === self::$validatedCipher) {
            return $cipher;
        }

        if (! in_array($cipher, openssl_get_cipher_methods(), true)) {
            throw new \Exception('The cipher method "'.$cipher.'" is not supported.');
        }

        self::$validatedCipher = $cipher;

        return $cipher;
    }

    public static function encrypt($string): string
    {
        $key = self::getKey();

        $encrypted = openssl_encrypt($string, self::getCipher(), $key, 0, self::ivFor($key));

        return base64_encode($encrypted);
    }

    /**
     * Returns '' when the value cannot be decrypted with the current key and
     * cipher: openssl_decrypt() returns false rather than throwing. Callers
     * must not treat an empty result as a legitimately empty plaintext.
     */
    public static function decrypt($encryptedString): string
    {
        $key = self::getKey();

        $decrypted = openssl_decrypt(base64_decode($encryptedString), self::getCipher(), $key, 0, self::ivFor($key));

        return $decrypted === false ? '' : $decrypted;
    }

    /**
     * Forget the cached cipher validation and IV. A key change is detected
     * automatically, so this is only needed by tests that want the next call
     * to start cold (for example after swapping the configured cipher).
     */
    public static function flushCache(): void
    {
        self::$validatedCipher = null;
        self::$ivKey = null;
        self::$iv = null;
    }

    private static function ivFor(string $key): string
    {
        if ($key !== self::$ivKey) {
            self::$ivKey = $key;
            self::$iv = substr(md5($key), 0, 16);
        }

        return self::$iv;
    }
}
