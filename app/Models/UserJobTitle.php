<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserJobTitle extends Model
{
    protected $table = 'user_job_titles';
    protected $guarded = [];
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'job_title_id',
        'pinned',
    ];

    public function jobID()
    {
        return $this->belongsTo(JobTitle::class, 'job_title_id');
    }
}
