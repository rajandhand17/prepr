<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabProgramTypeModes extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lab_program_type_modes';
    protected $fillable = [
        'lab_program_id',
        'type_mode',
        'value',
    ];
}
