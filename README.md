# Laravel Encryption

[![Latest Version on Packagist](https://img.shields.io/packagist/v/canaryau/laravel-encryption.svg?style=flat-square)](https://packagist.org/packages/canaryau/laravel-encryption)
[![Total Downloads](https://img.shields.io/packagist/dt/canaryau/laravel-encryption.svg?style=flat-square)](https://packagist.org/packages/canaryau/laravel-encryption)
![GitHub Actions](https://github.com/canaryau/laravel-encryption/actions/workflows/main.yml/badge.svg)

A package to easily encrypt & decrypt model fields in Laravel using OpenSSL.

## Key Features

- Encrypt and decrypt module attributes easily
- Minimal configuration
- Comes with a selection of eloquent builders for easy queries
- Growing support for casting encryptable data

## Installation

You can install the package via composer:

```bash
composer require canaryau/laravel-encryption
```

## Usage

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Joelwmale\LaravelEncryption\Traits\EncryptsAttributes;

class User extends Model
{
    use EncryptsAttributes;

    protected $encryptableAttributes = [
        'first_name',
        'last_name',
        'date_of_birth',
        'email_verified_at',
    ];

    protected $encryptableCasts = [
        'date_of_birth' => 'date',
        'email_verified_at' => 'datetime'
    ];
}
```

## Configuration

### Publshing The Configuration

The configuration file looks like this:

```php
return [
    /**
     * Enable or disable the encryption.
     */
    'enabled' => env('LARAVEL_ENCRYPTION_ENABLED', true),

    /**
     * The encryption key.
     *
     * Default: your app key.
     */
    'key' => env('LARAVEL_ENCRYPTION_KEY', null),

    /**
     * The encryption cipher.
     *
     * Supports any cipher method supported by openssl_get_cipher_methods().
     *
     * Default: AES-256-CBC.
     */
    'cipher' => env('LARAVEL_ENCRYPTION_CIPHER', 'AES-256-CBC'),
];
```

If you need to make any changes to the configuration, feel free to publish the configuration file.

```sh
php artisan vendor:publish --provider="Joelwmale\LaravelEncryption\LaravelEncryptionServiceProvider"
```

### Configure Model Attributes To Be Encrypted

This package will only encrypt fields within the `$encryptableAttributes` array, leaving the rest of the model unencrypted and untouched.

```php
protected $encryptableAttributes = [
    'first_name',
    'last_name',
];
```

This is useful for scenarios where compliance only requires you encrypting specific values and not your entire database.

### Casting Encrypted Values

Due to the fact that encrypted values can be quite long, you will need to store them as `text` fields, meaning you have to give way to native column types.

Also due to the way Laravel handles casting (and the priority it takes) you cannot right now use native casts with encrypted fields.

So we use our own array to determine what fields should be casted to what:

```php
protected $encryptableCasts = [
    'date_of_birth' => 'date',
];
```

#### Supported casts

Cast support is still growing, and will be added as time goes on. 

- date
- datetime
- json

### Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
