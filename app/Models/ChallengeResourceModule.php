<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallengeResourceModule extends Model
{
    use HasFactory;

    protected $table = 'challenge_resource_modules';

    protected $fillable = [
        'challenge_id',
        'resource_module_id',
    ];

    /**
     * Relationship with the Challenge model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function challenge()
    {
        return $this->belongsTo(Challenge::class, 'challenge_id');
    }

    /**
     * Relationship with the ResourceModule model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function resourceModule()
    {
        return $this->belongsTo(ResourceModule::class, 'resource_module_id');
    }
}
