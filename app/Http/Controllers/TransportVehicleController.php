<?php

namespace App\Http\Controllers;

use App\TransportVehicle;
use App\TransportRoute;
use Illuminate\Http\Request;
use Validator;

class TransportVehicleController extends Controller
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
        $user = auth()->user();
        $permission = $user->can('transport-vehicle-list');
        if(!$permission) {
            abort(403);
        }

        $transportvehicle = TransportVehicle::orderBy('id', 'asc')->get();
        $transportroute = TransportRoute::orderBy('id', 'asc')->get();

        return view('Transport.transport_vehicles',compact('transportroute', 'transportvehicle'));
    }
    

    public function store(Request $request)
    {

        $user = auth()->user();
        $permission = $user->can('transport-vehicle-create');
        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }


        $transportvehicle=new TransportVehicle;
        $transportvehicle->vehicle_type=$request->input('vehicle_type'); 
        $transportvehicle->vehicle_number=$request->input('vehicle_number');
        $transportvehicle->vehicle_owner=$request->input('vehicle_owner');
        $transportvehicle->vehicle_driver=$request->input('vehicle_driver');      
        $transportvehicle->save();
        
        return response()->json(['success' => 'Vehicle Added successfully.']);
    }

    public function edit($id)
    {
        if(request()->ajax())
        {
            $user = auth()->user();
            $permission = $user->can('transport-vehicle-edit');
            if(!$permission) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $data = TransportVehicle::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }
    public function update(Request $request, TransportVehicle $transportvehicle)
    {
        $user = auth()->user();
        $permission = $user->can('transport-vehicle-edit');
        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }


        $form_data = array(
            'vehicle_type'        =>  $request->vehicle_type,
            'vehicle_number'        =>  $request->vehicle_number,
            'vehicle_owner'        =>  $request->vehicle_owner,
            'vehicle_driver'        =>  $request->vehicle_driver,
            
        );

        TransportVehicle::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Vehicle is successfully updated']);
    }
    
    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('transport-vehicle-delete');
        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = TransportVehicle::findOrFail($id);
        $data->delete();
    }
}
