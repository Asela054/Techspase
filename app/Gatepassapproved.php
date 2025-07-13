<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gatepassapproved extends Model
{
    protected $table = 'gate_pass_approved';
    protected $primaryKey = 'id';

    protected $fillable =[ 'emp_id','date','minites_count','status','approve_status','created_by','updated_by','approved_by' ];
}
