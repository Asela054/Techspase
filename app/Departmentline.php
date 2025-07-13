<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Departmentline extends Model
{
    protected $table = 'department_lines';
    protected $primaryKey = 'id';

    protected $fillable =[
        'department_id','line','status'
         
    ];
}
