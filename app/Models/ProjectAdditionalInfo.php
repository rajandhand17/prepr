<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectAdditionalInfo extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'project_additional_info';
    protected $fillable = [
        'project_id',
        'category_id',
        'industry_id',
        'verticals_id',
        'type_id',
        'stage_id',
        'status_id',
    ];

    public function getCategory()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function getIndustry()
    {
        return $this->belongsTo(ProjectIndustry::class, 'industry_id', 'id');
    }

    public function getVerticals()
    {
        return $this->belongsTo(ProjectVertical::class, 'verticals_id', 'id');
    }

    public function getType()
    {
        return $this->belongsTo(ProjectType::class, 'type_id', 'id');
    }

    public function getStage()
    {
        return $this->belongsTo(ProjectStage::class, 'stage_id', 'id');
    }

    public function getStatus()
    {
        return $this->belongsTo(ProjectStatus::class, 'status_id', 'id');
    }
}
