<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class DailyHRSummary extends Model
{
     protected $table = 'daliy_hrsummary';
     protected $primaryKey = 'id';

    protected $fillable = ['date', 'attendace_count', 'absent_count', 'leave_count', 'nopay_count','late_count', 'status', 'created_by', 
    'updated_by', 'created_at', 'updated_at'];


        public function get_attendancecount($date)
        {
            $attendanceCount = DB::table('attendances')
                                ->where('date', $date)
                                ->whereNull('deleted_at')
                                ->count();
            
            return $attendanceCount;
        }

         public function get_leavecount($date)
        {
            $leavecount = DB::table('leaves')
                                ->where('leave_from', '<=', $date)
                                ->where('leave_to', '>=', $date)
                                ->where('leave_type', '!=', 3)
                                ->where('status', 'Approved')
                                ->count();
            
            return $leavecount;
        }

         public function get_nopaycount($date)
        {
            $nopaycount = DB::table('leaves')
                                ->where('leave_from', '<=', $date)
                                ->where('leave_to', '>=', $date)
                                ->where('leave_type', 3)
                                ->where('status', 'Approved')
                                ->count();
            
            return $nopaycount;
        }

        public function get_latecount($date)
        {
            $latecountcount = DB::table('employee_late_attendances')
                                 ->where('date', $date)
                                ->where('is_approved', 1)
                                ->count();
            
            return $latecountcount;
        }

}
