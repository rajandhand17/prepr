<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabProgram extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table="lab_programs";

    protected $fillable=[
        "language",
        "title",
        "description",
        "lab_id",
        "user_id",
        "media",
        "privacy",
        "status",
        "is_auto_created",
        "prize",
        "points",
        "trophy",
    ];
}
