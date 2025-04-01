<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(){
        //this method will return all active services 
        $projects=Project::where('status',1)
        ->orderBy('created_at','DESC')
        ->get();
        return response()->json([
            'status' => true,
            'data' =>  $projects
        ]);
       
    }

      //this method will return all active services 
    public function service(){
      
        $projects=Project::where('status',1)
        ->orderBy('created_at','DESC')
        ->get();
        return response()->json([
            'status' => true,
            'data' =>  $projects
        ]);
       
    }

    public function newprojects(Request $request){
        //this method will return latest active services 
        $projects = Project::where('status', 1)
        ->orderBy('created_at', 'DESC')
        ->take($request->get('limit'))
        ->get();

        return response()->json([
            'status' => true,
            'data' =>  $projects
        ]);
    }


}
