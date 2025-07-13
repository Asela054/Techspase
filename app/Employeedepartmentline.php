<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employeedepartmentline extends Model
{
    protected $table = 'employee_line';
    protected $primaryKey = 'id';

    protected $fillable =[
        'emp_id','line_id','date','status','created_by','updated_by'
         
    ];
}
