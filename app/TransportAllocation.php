<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransportAllocation extends Model
{
    protected $table = 'transport_allocations';
    protected $primarykey = 'id';

    protected $fillable =[

        'date','status','created_by', 'updated_by','created_at','updated_at'
    ];
}
