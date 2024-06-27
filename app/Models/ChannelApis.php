<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChannelApis extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'channel_apis';
    protected $guarded = [];

    protected $hidden = [
        'deleted_at',
    ];

    public function channelVendors()
    {
        return $this->belongsToMany(ChannelVendor::class, 'channel_vendor_api_access', 'channel_api_id', 'channel_vendor_id');
    }
}
