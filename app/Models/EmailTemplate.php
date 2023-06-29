<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'email_templates';

    protected $fillable = [
        'template_type',
        'module_type',
        'subject',
        'fr_CA_subject',
        'body_content',
        'fr_CA_body_content',
    ];
}
