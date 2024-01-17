<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPatent extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'user_patents';

    protected $fillable = [
        'user_id', 'title', 'name', 'patent_date', 'description',
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
