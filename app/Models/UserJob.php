<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserJob extends Model
{
    protected $table = 'user_jobs';
    protected $guarded = [];
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'job_id',
        'pinned',
    ];

    public function jobID()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }
}
