<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceCollectionForLabAndChallenge extends Model
{
    use HasFactory;

    use SoftDeletes;
    
    protected $fillable = ['user_id','lab_id','challenge_id','resource_collection_id'];

   protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

}
