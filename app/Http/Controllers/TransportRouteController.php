<?php

namespace App\Http\Controllers;

use App\TransportRoute;
use Illuminate\Http\Request;
use Validator;

class TransportRouteController extends Controller
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
        $permission = $user->can('transport-route-list');
        if(!$permission) {
            abort(403);
        }

        $transportroute = TransportRoute::orderBy('id', 'asc')->get();
        return view('Transport.transport_routes',compact('transportroute'));
    }

    public function store(Request $request)
    {

        $user = auth()->user();
        $permission = $user->can('transport-route-create');
        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }


        $transportroute=new TransportRoute;
        $transportroute->name=$request->input('name');       
        $transportroute->from=$request->input('from');       
        $transportroute->to=$request->input('to');
        $transportroute->save();
        
        return response()->json(['success' => 'Route Added successfully.']);
    }

    public function edit($id)
    {
        if(request()->ajax())
        {
            $user = auth()->user();
            $permission = $user->can('transport-route-edit');
            if(!$permission) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $data = TransportRoute::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }
    public function update(Request $request, TransportRoute $transportroute)
    {
        $user = auth()->user();
        $permission = $user->can('transport-route-edit');
        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }


        $form_data = array(
            'name'    =>  $request->name,
            'from'        =>  $request->from,
            'to'        =>  $request->to,
            
        );

        TransportRoute::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Route is successfully updated']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('transport-route-delete');
        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = TransportRoute::findOrFail($id);
        $data->delete();
    }
}
