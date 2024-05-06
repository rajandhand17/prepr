<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirmeetAuthToken extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'airmeet_auth_tokens';

    /**
     * @var string[]
     */
    protected $fillable = [
        'token',
        'expire_at',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'expire_at' => 'datetime',
    ];
}
