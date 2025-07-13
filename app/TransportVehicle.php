<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransportVehicle extends Model
{
    protected $table = 'transport_vehicles';
    protected $primaryKey = 'id';
    protected $fillable = ['vehicle_type', 'vehicle_number', 'vehicle_owner', 'vehicle_driver' , 'created_at', 'updated_at' ];

}
