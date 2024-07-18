<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Explore extends Model
{
    use HasFactory;
  //  use SoftDeletes;

    protected $table = 'explore_page_data';

    protected $fillable = [
        'comp_type',
        'comp_id',
        'title',
        'description',
        'action_button',
        'role',
        'media_type',
        'media'
    ];
}
