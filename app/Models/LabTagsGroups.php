<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabTagsGroups extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lab_tags_groups';
    protected $fillable = [
        'lab_id',
        'foreign_id',
        'type',
    ];

    public function tags()
    {
        return $this->hasOne(Tag::class, 'id', 'foreign_id');
    }
}
