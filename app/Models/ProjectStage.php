<?php

namespace App\Models;

use App\Helpers\LanguageColumnHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class ProjectStage extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'project_stages';

    protected $fillable = [
        'title',
        'fr_CA_title',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
