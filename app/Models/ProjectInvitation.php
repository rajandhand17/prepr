<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectInvitation extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table="project_invitations";

    protected $fillable=[
        "type","project_id","email","status"
    ];

    protected $hidden=[
        "created_at","updated_at","deleted_at",
    ];


}
