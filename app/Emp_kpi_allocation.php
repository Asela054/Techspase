<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Emp_kpi_allocation extends Model
{
    protected $primarykey = 'id';

    protected $fillable =[

        'year_id','measurement_id','department_id','emp_id','empfigure','status','created_by', 'updated_by','created_at','updated_at'
    ];
}
