<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceData extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table="resource_datas";

    protected $fillable=[
        'admin_challenge_id','resource_datas_id',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

}
