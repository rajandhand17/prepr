<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectHistory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'project_histories';
    protected $fillable = [
        'project_id',
        'user_id',
        'activity',
        'created_at',
        'updated_at'
    ];

    public function history()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
}
