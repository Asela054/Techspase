<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Otallocation extends Model
{
    protected $table = 'ot_allocation';
    protected $primarykey = 'id';

    protected $fillable =[

        'date','time_from','time_to','transport','meal','status','created_by', 'updated_by','created_at','updated_at'
    ];
}
