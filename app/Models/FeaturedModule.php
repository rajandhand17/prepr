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
        'module_type',
        'module_id',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getLabs()
    {
        return $this->belongsTo(Lab::class, 'module_id', 'id');
    }
}
