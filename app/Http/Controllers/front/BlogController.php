<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(){
        //this method will return all active blogs 
        $blogs=Blog::where('status',1)
        ->orderBy('created_at','DESC')
        ->get();
        return response()->json([
            'status' => true,
            'data' => $blogs
        ]);
       
    }


     // This method returns a single blog by ID
     public function blog($id){
        $blog = Blog::where('status', 1)
            ->where('id', $id)
            ->first();

        if (!$blog) {
            return response()->json([
                'status' => false,
                'message' => 'Blog not found'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'data' => $blog
        ]);
    }

    
    public function blogBySlug($slug)
    {
    // Fetch the blog by slug
    $blog = Blog::where('status', 1)
        ->where('slug', $slug)
        ->first();

    if (!$blog) {
        return response()->json([
            'status' => false,
            'message' => 'Blog not found'
        ], 404);
    }

    return response()->json([
        'status' => true,
        'data' => $blog
    ]);
    }

    public function newblogs(Request $request){
        //this method will return latest active blogs 
        $blogs = Blog::where('status', 1)
        ->orderBy('created_at', 'DESC')
        ->take($request->get('limit'))
        ->get();

        return response()->json([
            'status' => true,
            'data' => $blogs
        ]);
    }


}
