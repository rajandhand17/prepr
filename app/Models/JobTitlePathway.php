<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobTitlePathway extends Model
{
    protected $table = 'job_title_pathways';

    protected $fillable = [
        'lightcast_pathway_id', 'name', 'fr_CA_name', 'job_level', 'mean_salary',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
