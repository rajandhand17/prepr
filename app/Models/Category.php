<?php

namespace App\Models;

use App\Helpers\LanguageColumnHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class Category extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'categories';

    protected $fillable = [
        'title',
        'fr_CA_title',
        'components',
        'parent_id',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function parent()
    {
        return $this->hasOne(self::class, 'id', 'parent_id');
    }
}
