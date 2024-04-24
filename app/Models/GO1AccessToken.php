<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GO1AccessToken extends Model
{
    use HasFactory;

    protected $table = 'go1_access_token';

    protected $fillable = ['access_token'];
}
