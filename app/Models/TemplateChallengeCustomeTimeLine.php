<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateChallengeCustomeTimeLine extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'template_challenge_custom_timelines';
    protected $fillable = [
        'template_challenge_id',
        'custom_timelines_title',
        'custom_timelines_date',
        'custom_timelines_description',
        'custom_timelines_duration',
        'schedule_custom_notify',
    ];
}
