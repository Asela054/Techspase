<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $table = 'leaves';


    protected $fillable = [
        'emp_id',
        'leave_type',
        'leave_from',
        'leave_to',
        'no_of_days',
        'half_short',
        'reson',
        'comment',
        'emp_covering',
        'leave_approv_person',
        'leave_category',
        'status',
        'request_id'
    ];    
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id', 'emp_id');
    }

    public function covering_employee()
    {
        return $this->belongsTo(Employee::class, 'emp_covering', 'emp_id');
    }

    public function approve_by()
    {
        return $this->belongsTo(Employee::class, 'leave_approv_person', 'emp_id');
    }

}
