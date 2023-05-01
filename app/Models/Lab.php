<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lab extends Model
{
    use HasFactory;

    public function list($request)
    {
        $data=static::get();
        return $data;
    }

    public function create($request)
    {
        
    }
}
