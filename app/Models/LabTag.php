<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabTag extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table="lab_tag";

    protected $fillable = [
        'user_id',
        'lab_id',
        'tag',
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
