<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabAddress extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lab_address';

    protected $fillable = [
        'lab_id',
        'latitute',
        'longitude',
        'address',
        'city',
        'country',
    ];
}
