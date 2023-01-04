<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPersonal extends Model
{
    use HasFactory;
    
    use SoftDeletes;

    protected $table="user_personals";

    protected $fillable =[
        'user_id','about','gender','date_of_birth','age','user_type','language','recent_immigrant','visible_minority','disability',
   ];

   protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

  
}
