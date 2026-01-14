<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Canaryau\LaravelEncryption\Traits\EncryptsAttributes;

class TestUser extends Model
{
    use EncryptsAttributes;
    use HasFactory;

    protected $guarded = [];

    protected $table = 'test_users';

    protected $encryptableAttributes = [
        'date_of_birth',
        'email_verified_at',
    ];
}
