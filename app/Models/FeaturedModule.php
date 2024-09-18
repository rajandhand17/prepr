<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeaturedModule extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'featured_module';

    protected $fillable = [
        'title',
        'description',
        'button_text',
        'role',
        'media_type',
        'media',
        'module_type',
        'module_id',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    
    public function getMediaAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }
    public function getLabs()
    {
        return $this->belongsTo(Lab::class, 'module_id', 'id')->where('module_type','0');
    }
    
}
