<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabTemplateAddress extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table="template_lab_address";

    protected $fillable=[
        "template_lab_id",
        "latitude",
        "longitude",
        "address",
        "city",
        "country",
    ];
}
