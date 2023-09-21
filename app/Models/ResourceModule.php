<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceModule extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table="resource_modules";

    protected $fillable=[
        "uuid",
        "language",
        "user_id",
        "organization_id",
        "title",
        "slug",
        "description",
        "status",
        "is_global",
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function detailed(){
        return $this->belongsTo(ResourceModuleDetail::class,'resource_module_id','id');
    }

    public function users(){
        return $this->hasMany(User::class,'user_id','id');
    }
}
