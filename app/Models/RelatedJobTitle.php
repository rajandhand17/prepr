<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelatedJobTitle extends Model
{
    protected $table = 'related_job_titles';

    protected $fillable = [
        'job_title_id',
        'related_job_title_id',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
