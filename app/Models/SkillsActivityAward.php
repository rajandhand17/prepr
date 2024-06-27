<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SkillsActivityAward extends Model
{
    use HasFactory;

    protected $table = 'skills_activity_awards';
    protected $guarded = [];

    protected $fillable = [
        'name',
        'fr_CA_name',
        'skill',
        'description',
        'fr_CA_description',
        'image',
        'points',
        'challenge_participation_awards',
        'challenge_win_awards',
        'challenge_path_awards',
        'lab_program_awards',
        'resource_group_awards'
    ];

    public function getImageAttribute($value)
    {
        $path = Storage::cloud()->url($value);
        if ($path === env('AWS_URL')) {
            return '';
        }
        return $path;
    }
}
