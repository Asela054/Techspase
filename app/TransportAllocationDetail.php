<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransportAllocationDetail extends Model
{
    protected $table = 'transport_allocation_details';
    protected $primarykey = 'id';

    protected $fillable =[

        'transport_allocation_id' ,	'emp_id','route_id','vehicle_id','status','created_by', 'updated_by','created_at','updated_at'
    ];
}
