<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransportRoute extends Model
{
    protected $table = 'transport_routes';
    protected $primaryKey = 'id';
    protected $fillable = ['name' , 'from' , 'to' , 'created_at', 'updated_at' ];
}
