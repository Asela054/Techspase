<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Otallocationdetail extends Model
{
    protected $table = 'ot_allocationdetails';
    protected $primarykey = 'id';

    protected $fillable =[

        'ot_allocation_id','emp_id','time_from','time_to','status','created_by', 'updated_by','created_at','updated_at'
    ];
}
