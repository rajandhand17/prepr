<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelatedJob extends Model
{
    protected $table = 'related_jobs';

    protected $fillable = [
        'job_id',
        'related_job_id',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
