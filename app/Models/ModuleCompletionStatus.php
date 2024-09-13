<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuleCompletionStatus extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'module_completion_statuses';
    protected $fillable = [
        'user_id',
        'module_id',
        'module_type',
        'status',
        'is_completed',
        'percentage',
    ];

    /**
     * @return BelongsTo
     */
    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class, 'module_id', 'id')
            ->where('module_type', '=', config('constants.module_type.challenges'));
    }

    /**
     * @return BelongsTo
     */
    public function lab(): BelongsTo
    {
        return $this->belongsTo(Lab::class, 'module_id', 'id')
            ->where('module_type', '=', config('constants.module_type.labs'));
    }

    /**
     * @return BelongsTo
     */
    public function challengePath(): BelongsTo
    {
        return $this->belongsTo(ChallengePath::class, 'module_id', 'id')
            ->where('module_type', '=', config('constants.module_type.challenge_paths'));
    }

    /**
     * @return BelongsTo
     */
    public function resourceModule(): BelongsTo
    {
        return $this->belongsTo(ResourceModule::class, 'module_id', 'id')
            ->where('module_type', '=', config('constants.module_type.resource_modules'));
    }

    /**
     * @return BelongsTo
     */
    public function resourceGroup(): BelongsTo
    {
        return $this->belongsTo(ChallengePath::class, 'module_id', 'id')
            ->where('module_type', '=', config('constants.module_type.resource_group'));
    }

    /**
     * @return BelongsTo
     */
    public function resourceCollection(): BelongsTo
    {
        return $this->belongsTo(ChallengePath::class, 'module_id', 'id')
            ->where('module_type', '=', config('constants.module_type.resource_collections'));
    }
}
