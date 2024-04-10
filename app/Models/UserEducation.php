<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserEducation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'user_educations';

    protected $fillable = [
        'user_id', 'university', 'degree', 'start_date', 'end_date', 'address', 'state', 'country', 'description',
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
