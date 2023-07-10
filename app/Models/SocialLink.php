<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialLink extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'social_links';

    protected $fillable = [
        'title',
        'icon',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getSocialLinks($language = 'en', $search = null)
    {
        try {
            $social_link_list = static::select('id', 'title', 'icon');
            //Search categories based on user input
            if ($search != null) {
                $social_link_list = $social_link_list->where('title', 'like', '%'.$search.'%');
            }

            //take 20 results based from the table
            $social_link_list = $social_link_list->take(20)->get();

            //check if there are any results
            if (!$social_link_list->isEmpty()) {
                return $social_link_list;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
