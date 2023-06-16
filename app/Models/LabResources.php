<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabResources extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'lab_id',
        'resources_id',
        'collection_id',
        'group_id',
        'status',
        'sequence_no',
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
