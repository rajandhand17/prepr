<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabCondition extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'lab_conditions';

    protected $fillable = [
        'title',
        'fr_CA_title',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getLabConditions($language = 'en', $search = null){
        try {
            $labConditions = static::select('id', 'title');
            if ($search != null) {
                $labConditions = $labConditions->where('title', 'like', '%'.$search.'%');
            }
            $labConditions = $labConditions->take(20)->get();
            //  return $host;
            if (!$labConditions->isEmpty()) {
                return $labConditions;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
