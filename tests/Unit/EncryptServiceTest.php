<?php

use Canaryau\LaravelEncryption\Services\EncryptService;

beforeEach(function () {
    EncryptService::flushCache();
});

it('produces the same ciphertext format as a direct openssl call', function () {
    // Data already at rest was written by the pre-cache implementation, which
    // derived the IV from the key exactly like this. Any drift here would make
    // every stored value unreadable.
    $key = EncryptService::getKey();
    $iv = substr(md5($key), 0, 16);

    $expected = base64_encode(openssl_encrypt('Jane Citizen', 'aes-256-cbc', $key, 0, $iv));

    expect(EncryptService::encrypt('Jane Citizen'))->toBe($expected);
    expect(EncryptService::decrypt($expected))->toBe('Jane Citizen');
});

it('round-trips repeatedly with the cached cipher and iv', function () {
    foreach (['a', 'longer value with spaces', '1990-01-01', json_encode(['k' => 'v'])] as $plain) {
        expect(EncryptService::decrypt(EncryptService::encrypt($plain)))->toBe($plain);
    }
});

it('returns an empty string for a value that cannot be decrypted', function () {
    expect(EncryptService::decrypt('not-real-ciphertext'))->toBe('');
    expect(EncryptService::decrypt(base64_encode(random_bytes(7))))->toBe('');
});

it('still rejects an unsupported cipher after a valid one has been cached', function () {
    expect(EncryptService::getCipher())->toBe('aes-256-cbc');

    config(['laravel_encryption.cipher' => 'not-a-cipher']);

    expect(fn () => EncryptService::getCipher())->toThrow(Exception::class, 'not-a-cipher');

    config(['laravel_encryption.cipher' => 'AES-256-CBC']);

    expect(EncryptService::getCipher())->toBe('aes-256-cbc');
});

it('honours a key change without an explicit cache flush', function () {
    $ciphertext = EncryptService::encrypt('secret');

    config(['laravel_encryption.key' => 'a-completely-different-key-value']);

    expect(EncryptService::encrypt('secret'))->not->toBe($ciphertext);
    expect(EncryptService::decrypt($ciphertext))->not->toBe('secret');

    config(['laravel_encryption.key' => null]);

    expect(EncryptService::decrypt($ciphertext))->toBe('secret');
});
