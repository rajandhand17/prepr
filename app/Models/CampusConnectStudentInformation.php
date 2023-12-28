<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampusConnectStudentInformation extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'campus_connect_student_information';
    protected $fillable = [
        'user_id',
        'student_number',
        'current_program',
        'current_degree',
        'current_institution',
        'institution_type',
        'enrollment_status',
        'current_year',
    ];
}
