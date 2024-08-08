<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabTypeModes extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lab_type_modes';
    protected $fillable = [
        'lab_id',
        'type_mode',
        'value',
    ];
}
