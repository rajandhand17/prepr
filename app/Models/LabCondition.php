<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabCondition extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'lab_conditions';

    protected $fillable = [
        'title',
        'fr_CA_title',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
