<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['student_name', 'supervisor', 'title', 'project_type', 'completion_year', 'pdf_file'];
}
