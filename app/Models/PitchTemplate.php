<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PitchTemplate extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'pitch_templates';

    protected $fillable = [
        'title',
        'challenge_id',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getPitchTemplates($language = 'en', $search = null)
    {
        try {
            $pitch_temple_list = static::select('id', 'title');
            //Search Pitch templete based on user input
            //Search categories based on user input
            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $pitch_temple_list = $pitch_temple_list->where($column_name, 'like', '%'.$search.'%');
            }

            //take 20 results based from the table
            $pitch_temple_list = $pitch_temple_list->take(20)->get();

            //check if there are any results
            if (!$pitch_temple_list->isEmpty()) {
                return $pitch_temple_list;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
