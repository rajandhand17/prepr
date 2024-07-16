<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabSkillsGroupsStack extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lab_skills_groups_stack';

    protected $fillable = [
        'lab_id',
        'foreign_id',
        'type',
    ];

    public function lab()
    {
        return $this->belongsTo(Lab::class);
    }
}
