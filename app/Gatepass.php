<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gatepass extends Model
{
    protected $table = 'gate_pass';
    protected $primaryKey = 'id';

    protected $fillable =[ 'emp_id','date','intime','offtime','minites_count','status','approve_status','created_by','updated_by','approved_by' ];
}
