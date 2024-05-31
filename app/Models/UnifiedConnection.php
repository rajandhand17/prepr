<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string $model_type
 * @property int    $model_id
 * @property string $connection_id
 * @property string $connection_type
 * @property string $usage_type
 * @property int    $user_id
 * @property string $last_used_at
 * @property int    $id
 */
class UnifiedConnection extends Model
{
    use HasFactory;

    protected $table = 'unified_connections';

    protected $fillable = [
        'model_type',
        'model_id',
        'connection_id',
        'connection_type',
        'usage_type',
        'user_id',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    /**
     * @return MorphTo
     */
    public function unifiedConnection(): MorphTo
    {
        return $this->morphTo('model');
    }
}
