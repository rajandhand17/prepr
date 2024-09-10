<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabHistory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lab_histories';
    protected $fillable = [
        'module_id',
        'user_id',
        'activity',
    ];

    public function history()
    {
        return $this->belongsTo(Lab::class, 'lab_id', 'id');
    }
}
