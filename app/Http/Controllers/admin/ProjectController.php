<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function index(){

    }

    public function store(Request $request)
    {
    // Validate input data
    $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:projects,slug',
        'short_desc' => 'nullable|string|max:500',
        'content' => 'nullable|string',
        'construction_type' => 'required|in:residential,commercial,industrial,infrastructure,renovation,educational,transportation,others',
        'sector' => 'required|in:private,public,governmental',
        'location' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Supports only image files (max 2MB)
        'status' => 'required|integer|in:0,1', // 0 = Inactive, 1 = Active
    ]);

    // Store project
    $project = new Project();
    $project->title = $request->title;
    $project->slug = $request->slug;
    $project->short_desc = $request->short_desc;
    $project->content = $request->content;
    $project->construction_type = $request->construction_type;
    $project->sector = $request->sector;
    $project->location = $request->location;
  
    // Handle image upload if provided
    // if ($request->hasFile('image')) {
    //     $imagePath = $request->file('image')->store('uploads/projects', 'public');
    //     $project->image = $imagePath;
    // }

    $project->status = $request->status;
    $project->save();

    return response()->json([
        'message' => 'Project created successfully!',
        'data' => $project
    ]);
}

public function update(){

}

public function destroy(){

}


}
