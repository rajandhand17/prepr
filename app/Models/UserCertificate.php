<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCertificate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'user_certificates';

    protected $fillable = [
        'user_id',
        'company',
        'name',
        'start_date',
        'end_date',
        'description',
    ];
}
