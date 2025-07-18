<?php

namespace App\Http\Controllers;

use App\EmployeeAttachment;
use App\Employee;
use App\EmployeeDependent;
use App\JobTitle;
use App\EmploymentStatus;
use App\EmployeeEducation;
use App\EmployeeExperience;
use App\EmployeeSkill;
use App\EmployeeLanguages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Response;

class EmployeeAttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->validate($request, array( 
                      
            'empattachment' => 'required|mimes:pdf,doc,docx|max:2048',
            'empcomment' => 'required|string|max:255', 
         ));

         if ($request->hasFile('empattachment')) {
            $image = $request->file('empattachment');
            $name = time().'_emp.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/attachment');
            $image->move($destinationPath, $name);

            
            $employeeattachment=new EmployeeAttachment;
            $id=$request->input('id');
            $employeeattachment->emp_id=$request->input('id');
            $employeeattachment->emp_ath_file_name= $name;
            $employeeattachment->emp_ath_type='employee';
            $employeeattachment->save();

            $employee = Employee::where('id',$id)->first();
            $jobtitles= JobTitle::orderBy('id', 'asc')->get();
            $employmentstatus= EmploymentStatus::orderBy('id', 'asc')->get();
            return view('Employee.viewEmployee',compact('employee','id','jobtitles','employmentstatus'));
           // return redirect('viewEmployee/'.$id);
           
        }
    }

    public function employeeAttachmentJson(Request $request)
    {
        $this->validate($request, array(
            'empattachment' => 'required|mimes:pdf,doc,docx|max:2048',
            //'empcomment' => 'required|string|max:255',
            'attachment_type' => 'required',
        ));

        if ($request->hasFile('empattachment')) {
            $image = $request->file('empattachment');
            $name = time().'_emp.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/attachment');
            $image->move($destinationPath, $name);


            $employeeattachment=new EmployeeAttachment;
            $id=$request->input('id');
            $employeeattachment->emp_id=$request->input('id');
            $employeeattachment->emp_ath_file_name= $name;
            $employeeattachment->attachment_type= $request->input('attachment_type');
            $employeeattachment->save();

            $employee = Employee::where('id',$id)->first();
            $attachments = EmployeeAttachment::with('attachment_type_rel')
            ->where('emp_id',$id)
            ->where('emp_ath_type', null)
                ->get();
            return view('Employee.viewFiles',compact('employee','id', 'attachments'));

        }
    }

    public function download_file($file)
    {
        //$filepath = public_path('images/filename.JPG');
        $filepath = public_path('attachment/'.$file);
        return Response::download($filepath);
    }

    public function destroy_attachment($id)
    {
        $att = EmployeeAttachment::where('emp_ath_id', $id)->delete();
        return response()->json(['success' => 'The Attachment Successfully Deleted']);
    }

    public function createcontact(Request $request)
    {
        $this->validate($request, array( 
                      
            'empattachment' => 'required|mimes:pdf,doc,docx|max:2048',
            'empcomment' => 'required|string|max:255', 
         ));

         if ($request->hasFile('empattachment')) {
            $image = $request->file('empattachment');
            $name = time().'_con.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/attachment');
            $image->move($destinationPath, $name);

            
            $employeeattachment=new EmployeeAttachment;
            $id=$request->input('id');
            $employeeattachment->emp_id=$request->input('id');
            $employeeattachment->emp_ath_file_name= $name;
            $employeeattachment->emp_ath_type='contact';
            $employeeattachment->save();

            $employee = Employee::where('id',$id)->first();

            return view('Employee.contactDetails',compact('employee','id'));
           
        }
    }

    public function createdependent(Request $request)
    {
        $this->validate($request, array( 
                      
            'empattachment' => 'required|mimes:pdf,doc,docx|max:2048',
            'empcomment' => 'required|string|max:255', 
         ));

         if ($request->hasFile('empattachment')) {
            $image = $request->file('empattachment');
            $name = time().'_depend.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/attachment');
            $image->move($destinationPath, $name);

            
            $employeeattachment=new EmployeeAttachment;
            $id=$request->input('id');
            $employeeattachment->emp_id=$request->input('id');
            $employeeattachment->emp_ath_file_name= $name;
            $employeeattachment->emp_ath_type='dependent';
            $employeeattachment->save();

           //$dependent = Employee::where('id',$id)->first();
           $dependent = EmployeeDependent::where('emp_id',$id)->get();

            return view('Employee.viewDependents',compact('dependent','id'));
           
        }
    }

    public function createimmigration(Request $request)
    {
        $this->validate($request, array( 
                      
            'empattachment' => 'required|mimes:pdf,doc,docx|max:2048',
            'empcomment' => 'required|string|max:255', 
         ));

         if ($request->hasFile('empattachment')) {
            $image = $request->file('empattachment');
            $name = time().'_imgra.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/attachment');
            $image->move($destinationPath, $name);

            
            $employeeattachment=new EmployeeAttachment;
            $id=$request->input('id');
            $employeeattachment->emp_id=$request->input('id');
            $employeeattachment->emp_ath_file_name= $name;
            $employeeattachment->emp_ath_type='immigration';
            $employeeattachment->save();

           //$dependent = Employee::where('id',$id)->first();
           $dependent = EmployeeDependent::where('emp_id',$id)->get();

            return view('Employee.viewImmigration',compact('dependent','id'));
           
        }
    }
    public function createqulification(Request $request){
        $this->validate($request, array( 
                      
            'empattachment' => 'required|mimes:pdf,doc,docx|max:2048',
            'empcomment' => 'required|string|max:255', 
         ));

         if ($request->hasFile('empattachment')) {
            $image = $request->file('empattachment');
            $name = time().'_imgra.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/attachment');
            $image->move($destinationPath, $name);

            
            $employeeattachment=new EmployeeAttachment;
            $id=$request->input('id');
            $employeeattachment->emp_id=$request->input('id');
            $employeeattachment->emp_ath_file_name= $name;
            $employeeattachment->emp_ath_type='Qualification';
            $employeeattachment->save();

           //$dependent = Employee::where('id',$id)->first();
          // $dependent = EmployeeDependent::where('emp_id',$id)->get();

          $education = EmployeeEducation::where('emp_id',$id)->get();
          $experience = EmployeeExperience::where('emp_id',$id)->get();
          $skill = EmployeeSkill::where('emp_id',$id)->get();
          $languages = EmployeeLanguages::where('emp_id',$id)->get();
  
          
         
          return view('Employee.viewQualification',compact('education','experience','skill','languages','id'));
            // return redirect('viewQualification/'.$id);
           
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EmployeeAttachment  $employeeAttachment
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeAttachment $employeeAttachment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeAttachment  $employeeAttachment
     * @return \Illuminate\Http\Response
     */
    public function edit(EmployeeAttachment $employeeAttachment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeAttachment  $employeeAttachment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeAttachment $employeeAttachment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeAttachment  $employeeAttachment
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeAttachment $employeeAttachment)
    {
        //
    }
}
