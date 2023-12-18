<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabTemplateSkillsGroupsStack extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table="template_lab_skills_groups_stack";

    protected $fillable=[
        "template_lab_id",
        "foreign_id",
        "type",
    ];
}
