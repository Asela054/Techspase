<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AttendanceInsert extends Model
{
    public function GeneralNightshiftemployee_insert(array $records, string $previousDate)
    {
        // Process permanent night shift employees
        foreach ($records as $record) {
            $timestamp = Carbon::parse($record['timestamp']);
            $attendancedate = $record['attdate'];
            $emp_id = $record['emp_id'];

            if ($timestamp->format('A') === 'AM') {
                $befordaycheck = DB::table('attendances')
                    ->whereDate('date', $previousDate)
                    ->whereDate('timestamp', $previousDate)
                    ->where('emp_id', $emp_id )
                    ->first();
                    
                if (!empty($befordaycheck)) {
                    $attendancedate = $previousDate;
                } else {
                    $newdatacheck = DB::table('attendances')
                        ->where('emp_id', $emp_id )
                        ->first();
                        
                    if (empty($newdatacheck)) {
                        $attendancedate = $previousDate;
                    }
                }
            }
            
          $attendance = Attendance::firstOrNew([
                'timestamp' => $timestamp,
                'emp_id' => $emp_id
            ]);
            
            $attendance->uid = $emp_id;
            $attendance->emp_id = $emp_id;
            $attendance->timestamp = $timestamp;
            $attendance->date = $attendancedate;
            $attendance->location = 1;
            
            return $attendance->save();

        }
    }

    public function Nightshiftempoyee_insert(array $records, string $previousDate, $dayshiftonduty)
    {

        // Process temporary night shift employees
        foreach ($records as $record) {
            $timestamp = Carbon::parse($record['timestamp']);
            $attendancedate = $record['attdate'];
            $emp_id = $record['emp_id'];

            // Subtract 30 mins for cutoff
            $ondutyTime = Carbon::parse($dayshiftonduty);
            $cutoffTime = $ondutyTime->copy()->subMinutes(30);

            // Temporary night shift logic for AM timestamps before cutoff
            if ($timestamp->format('A') === 'AM' && $timestamp->lt($cutoffTime)) {
                $attendancedate = $previousDate;
            }
        
         
            $attendance = Attendance::firstOrNew([
                'timestamp' => $timestamp,
                'emp_id' => $emp_id
            ]);
            
            $attendance->uid = $emp_id;
            $attendance->emp_id = $emp_id;
            $attendance->timestamp = $timestamp;
            $attendance->date = $attendancedate;
            $attendance->location = 1;
            
            return $attendance->save();

        }
    }

    public function ExtendedShiftemployee_insert(array $records, string $previousDate , $dayshiftonduty)
    {
        // Process extended shift employees
        foreach ($records as $record) {
            $timestamp = Carbon::parse($record['timestamp']);
            $attendancedate = $record['attdate'];
            $emp_id = $record['emp_id'];
          
              // Subtract 30 mins for cutoff
            $ondutyTime = Carbon::parse($dayshiftonduty);
            $cutoffTime = $ondutyTime->copy()->subMinutes(30);


            if ($timestamp->format('A') === 'AM' && $timestamp->hour >= 0 && $timestamp->lt($cutoffTime)) {
                $attendancedate = $previousDate;
            }
                
            $attendance = Attendance::firstOrNew([
                'timestamp' => $timestamp,
                'emp_id' => $emp_id
            ]);
            
            $attendance->uid = $emp_id;
            $attendance->emp_id = $emp_id;
            $attendance->timestamp = $timestamp;
            $attendance->date = $attendancedate;
            $attendance->location = 1;
            
            return $attendance->save();
        }
    }

    public function GeneralDayshiftemployee_insert(array $records, string $previousDate)
    {
        // Process day shift employees
        foreach ($records as $record) {
            $attendance = Attendance::firstOrNew([
                'timestamp' => Carbon::parse($record['timestamp']),
                'emp_id' => $record['emp_id']
            ]);
            
            $attendance->fill([
                'uid' => $record['emp_id'],
                'emp_id' => $record['emp_id'],
                'timestamp' => Carbon::parse($record['timestamp']),
                'date' => $record['attdate'],
                'location' => 1
            ])->save();
        }
    }
}
