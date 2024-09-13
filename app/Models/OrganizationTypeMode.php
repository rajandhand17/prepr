<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationTypeMode extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'organization_type_modes';
    protected $fillable = [
        'organization_id',
        'type_mode',
        'value',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }
}
