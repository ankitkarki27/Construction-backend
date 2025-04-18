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

      //this method will return all active project 
      public function project($id){
        $project = Project::where('status', 1)
            ->where('id', $id)
            ->first();

        if (!$project) {
            return response()->json([
                'status' => false,
                'message' => 'Project not found'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'data' => $project
        ]);
    }

    public function projectBySlug($slug)
    {
    // Fetch the project by slug
    $project = Project::where('status', 1)
        ->where('slug', $slug)
        ->first();

    if (!$project) {
        return response()->json([
            'status' => false,
            'message' => 'Project not found'
        ], 404);
    }
    
    return response()->json([
        'status' => true,
        'data' => $project
    ]);
    }
    

    public function newprojects(Request $request){
        //this method will return latest active projects 
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
