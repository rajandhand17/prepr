<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabMarketplaceExternalLink extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lab_marketplace_external_links';

    protected $fillable = [
        'lab_marketplace_id',
        'social_media_link',
        'social_link_id',

    ];
}
