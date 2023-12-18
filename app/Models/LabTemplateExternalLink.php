<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabTemplateExternalLink extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table="template_lab_external_links";

    protected $fillable=[
        "template_lab_id",
        "social_media_link",
        "social_link_id",

    ];
}
