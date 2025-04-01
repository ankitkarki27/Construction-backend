<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(){
        //this method will return all active services 
        $services=Service::where('status',1)
        ->orderBy('created_at','DESC')
        ->get();
        return response()->json([
            'status' => true,
            'data' => $services
        ]);
       
    }


     // This method returns a single service by ID
     public function service($id){
        $service = Service::where('status', 1)
            ->where('id', $id)
            ->first();

        if (!$service) {
            return response()->json([
                'status' => false,
                'message' => 'Service not found'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'data' => $service
        ]);
    }

    public function serviceBySlug($slug)
    {
    // Fetch the service by slug
    $service = Service::where('status', 1)
        ->where('slug', $slug)
        ->first();

    if (!$service) {
        return response()->json([
            'status' => false,
            'message' => 'Service not found'
        ], 404);
    }

    return response()->json([
        'status' => true,
        'data' => $service
    ]);
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
