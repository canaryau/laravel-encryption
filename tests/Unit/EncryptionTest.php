<?php

use Tests\Models\TestUser;
use Illuminate\Support\Facades\DB;
use Tests\Models\TestUserDateCast;
use Canaryau\LaravelEncryption\Services\EncryptService;

it('encrypts data', function () {
    $user = TestUser::create([
        'name' => 'John Doe',
        'email' => 'john@doe.com.au',
        'password' => 'password',
        'date_of_birth' => '1990-01-01',
    ]);

    $dbUser = DB::table('test_users')->where('id', $user->id)->first();

    expect($dbUser->date_of_birth)->not->toBe('1990-01-01');
});

it('decrypts data', function () {
    $user = TestUser::create([
        'name' => 'John Doe',
        'email' => 'john@doe.com.au',
        'password' => 'password',
        'date_of_birth' => '1990-01-01',
    ]);

    $user = TestUser::first();

    expect($user->date_of_birth)->toBe('1990-01-01');
});

it('doesnt encrypt data when disabled', function () {
    config(['laravel_encryption.enabled' => false]);

    $user = TestUser::create([
        'name' => 'John Doe',
        'email' => 'john@doe.com.au',
        'password' => 'password',
        'date_of_birth' => '1990-01-01',
    ]);

    $dbUser = DB::table('test_users')->where('id', $user->id)->first();

    expect($dbUser->date_of_birth)->toBe('1990-01-01');
});

it('encrypts data with casts (date)', function () {
    $user = TestUserDateCast::create([
        'name' => 'John Doe',
        'email' => 'john@doe.com.au',
        'password' => 'password',
        'date_of_birth' => '1990-01-01',
    ]);

    $dbUser = DB::table('test_users')->where('id', $user->id)->first();

    expect($dbUser->date_of_birth)->not->toBe('1990-01-01');
});

it('decrypts data with casts (date)', function () {
    $user = TestUserDateCast::create([
        'name' => 'John Doe',
        'email' => 'john@doe.com.au',
        'password' => 'password',
        'date_of_birth' => '1990-01-01',
    ]);

    $user = TestUserDateCast::first();

    expect($user->date_of_birth)->toBeInstanceOf(\Carbon\Carbon::class);
    expect($user->date_of_birth->toDateString())->toBe('1990-01-01');
});

it('encrypts data with casts (datetime)', function () {
    $emailVerifiedAt = now();

    $user = TestUserDateCast::create([
        'name' => 'John Doe',
        'email' => 'john@doe.com.au',
        'password' => 'password',
        'date_of_birth' => '1990-01-01',
        'email_verified_at' => $emailVerifiedAt,
    ]);

    $dbUser = DB::table('test_users')->where('id', $user->id)->first();

    expect($dbUser->email_verified_at)->not->toBe($emailVerifiedAt);
});

it('decrypts data with casts (datetime)', function () {
    $emailVerifiedAt = now();

    $user = TestUserDateCast::create([
        'name' => 'John Doe',
        'email' => 'john@doe.com.au',
        'password' => 'password',
        'date_of_birth' => '1990-01-01',
        'email_verified_at' => $emailVerifiedAt,
    ]);

    $user = TestUserDateCast::first();

    expect($user->email_verified_at)->toBeInstanceOf(\Carbon\Carbon::class);
    expect($user->email_verified_at->toDateTimeString())->toBe($emailVerifiedAt->toDateTimeString());
});

it('encrypts the same string every time', function () {
    $string = 'This is a string';
    $stringTwo = 'This is a string.';

    $encryptedString1 = EncryptService::encrypt($string);
    $encryptedString2 = EncryptService::encrypt($string);
    $encryptedString3 = EncryptService::encrypt($string);
    $encryptedString4 = EncryptService::encrypt($stringTwo);

    expect($encryptedString1)->toBe($encryptedString2);
    expect($encryptedString2)->toBe($encryptedString3);
    expect($encryptedString1)->toBe($encryptedString3);
    expect($encryptedString1)->not->toBe($encryptedString4);
});
