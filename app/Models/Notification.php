<?php

namespace App\Models;

use App\Models\Accessor\NotificationAccessor;
use App\Models\Builder\NotificationBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    use NotificationAccessor;

    protected $casts = [
        'id'   => 'string',
        'data' => 'array',
    ];

    /**
     * @param $query
     *
     * @return NotificationBuilder
     */
    public function newEloquentBuilder($query): NotificationBuilder
    {
        return new NotificationBuilder($query);
    }
}
