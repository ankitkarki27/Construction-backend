<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(){
        //this method will return all active services 
        $services=Service::where('status',1)->orderBy('created_at','DESC')->get();
        return response()->json([
            'status' => true,
            'data' => $services
        ]);
        // return  $services;
    }

    public function newservices(Request $request){
        //this method will return latest active services 
        $services = Service::where('status', 1)
        ->orderBy('created_at', 'DESC')
        ->take($request->get('limit'))
        ->get();

        return response()->json([
            'status' => true,
            'data' => $services
        ]);
    }
}
