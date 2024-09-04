<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DashboardLayout extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'dashboard_layouts';

    protected $fillable = [
        'user_id',
        'dashboard_type',
        'card_type',
        'is_active',
        'position_index',
    ];
}
