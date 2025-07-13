<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Http\Controllers\Controller;

class LateminitesautogenerateController extends Controller
{
    public function index(){

        $user = Auth::user();
        $permission = $user->can('Late-minites-manual-mark-list');
        if(!$permission){
            abort(403);
        }

        return view('Attendent.lateminitesautomark');
    }

    public function marklateattendance(Request $request){

        $user = Auth::user();
        $permission = $user->can('Lateminites-Approvel-apprve');

        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $date = $request->get('closedate');
        $company_id = $request->get('company_id');
        $department_id = $request->get('department_id');

        try {
            $auditAttendance = (new \App\AutoLateMark)->auto_late_attendace_mark_manual($date, $company_id, $department_id);
            
            return response()->json([
                'success' => 'Late Attendance successfully marked for ' . $date
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'errors' => ['Failed to mark late attendance: ' . $e->getMessage()]
            ], 500);
        }
    }
}
