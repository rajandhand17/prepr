<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int    $id
 * @property string $model_type
 * @property int    $model_id
 * @property string $airmeet_event_id
 * @property string $airmeet_event_url
 */
class AirmeetEvent extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'model_type',
        'model_id',
        'airmeet_event_id',
        'airmeet_event_url',
        'name',
    ];

    /**
     * @return MorphTo
     */
    public function airmeet(): MorphTo
    {
        return $this->morphTo('model');
    }
}
