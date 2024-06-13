<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChannelVendor extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'channel_vendors';
    protected $guarded = [];

    protected $hidden = [
        'deleted_at',
    ];

    public function channelApis()
    {
        return $this->belongsToMany(ChannelApis::class, 'channel_vendor_api_access', 'channel_vendor_id', 'channel_api_id');
    }
}
