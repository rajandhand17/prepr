<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerCompanies extends Model
{
    use HasFactory;

    protected $table = 'partner_companies';

    protected $fillable = [
        'title',
        'url',
        'media',
        'status',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
