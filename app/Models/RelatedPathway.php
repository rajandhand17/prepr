<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelatedPathway extends Model
{
    protected $table = 'related_pathways';

    protected $fillable = [
        'lightcast_pathway_id',
        'related_lightcast_pathway_id',
        'category',
    ];

    public $timestamps = true;
}
