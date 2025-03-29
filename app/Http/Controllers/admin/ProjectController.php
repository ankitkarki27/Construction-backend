<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ServiceImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;

class ProjectController extends Controller
{
    public function index()
    {
         // To Fetch all projects
        $projects = Project::orderBy('created_at', 'DESC')->get();
        return response()->json([
            'status' => true,
            'data' => $projects
        ]);
    }

    public function store(Request $request)
    {

    $request->merge(['slug'=>Str::slug($request->slug)]);

    // Validate input data
    $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:projects,slug',
        'short_desc' => 'nullable|string|max:500',
        'content' => 'nullable|string',
        'construction_type' => 'required|in:residential,commercial,industrial,infrastructure,renovation,educational,transportation,others',
        'sector' => 'required|in:private,public,governmental',
        'location' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'status' => 'required|integer|in:0,1',
    ]);

    // Store project
    $project = new Project();
    $project->title = $request->title;
    // $project->slug = $request->slug;
    $project->slug = Str::slug($request->slug);
    $project->short_desc = $request->short_desc;
    $project->content = $request->content;
    $project->construction_type = $request->construction_type;
    $project->sector = $request->sector;
    $project->location = $request->location;
    $project->status = $request->status;
    $project->save();

    if ($request->imageId > 0) {
        // ServiceImage model is for project too.It contains code for ptoject also
        $serviceImage = ServiceImage::find($request->imageId);
        if ($serviceImage) {
            $extArray = explode('.', $serviceImage->name);
            $ext = last($extArray);
            $fileName = strtotime('now') . $project->id . '.' . $ext;

            $manager = new ImageManager(Driver::class);
            $sourcePath = public_path('uploads/servicetemp/' . $serviceImage->name);

            // Generate small thumbnail
            $smallPath = public_path('uploads/projects/small/' . $fileName);
            $image = $manager->read($sourcePath);
            $image->coverDown(375, 400);
            $image->save($smallPath);

            // Generate large thumbnail
            $largePath = public_path('uploads/projects/large/' . $fileName);
            $image = $manager->read($sourcePath);
            $image->scaleDown(1200);
            $image->save($largePath);

            // Save image name in service model
            $project->image = $fileName;
            $project->save();
    }

    return response()->json([
        'status'=>true,
        'message' => 'Project created successfully!',
        'data' => $project
    ]);
    }
    }

        // fetch a single service by ID
        public function show($id)
        {
            $project = Project::find($id);
    
            // Check if service exists
            if (!$project) {
                return response()->json([
                    'status' => false,
                    'message' => 'Project not found'
                ]);
            }
    
            return response()->json([
                'status' => true,
                'data' => $project
            ]);
        }

        
        public function update(Request $request, $id)
        {
            // Find project by ID
            $project = Project::find($id);
        
            if (!$project) {
                return response()->json([
                    'status' => false,
                    'message' => 'Project not found'
                ], 404);
            }
        
            // Merge slug with a formatted version
            $request->merge(['slug' => Str::slug($request->slug)]);
        
            // Validate input data
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:projects,slug,' . $id,
                'short_desc' => 'nullable|string|max:500',
                'content' => 'nullable|string',
                'construction_type' => 'required|in:residential,commercial,industrial,infrastructure,renovation,educational,transportation,others',
                'sector' => 'required|in:private,public,governmental',
                'location' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'status' => 'required|integer|in:0,1',
            ]);
        
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ]);
            }
             // Store old image before updating
             $oldImage = $project->image;

            // Update project details
            $project->title = $request->title;
            // $project->slug = $request->slug;
            $project->slug = Str::slug($request->slug);
            $project->short_desc = $request->short_desc;
            $project->content = $request->content;
            $project->construction_type = $request->construction_type;
            $project->sector = $request->sector;
            $project->location = $request->location;
            $project->status = $request->status;
            $project->save();

             // Handle image update if provided
        if ($request->imageId > 0) {
            $serviceImage = ServiceImage::find($request->imageId);
            if ($serviceImage) {
                $extArray = explode('.', $serviceImage->name);
                $ext = last($extArray);
                $fileName = strtotime('now') . $project->id . '.' . $ext;

                $manager = new ImageManager(Driver::class);
                $sourcePath = public_path('uploads/servicetemp/' . $serviceImage->name);

                // Generate small thumbnail
                $destPath = public_path('uploads/projects/small/' . $fileName);
                $image = $manager->read($sourcePath);
                $image->coverDown(375, 400);
                // $image->coverDown(500, 600);
                $image->save($destPath);

                // Generate large thumbnail
                $destPath = public_path('uploads/projects/large/' . $fileName);
                $image = $manager->read($sourcePath);
                $image->scaleDown(1200);
                $image->save($destPath);

                // Save new image and delete old one
                $project->image = $fileName;
                $project->save();

                if ($oldImage) {
                    File::delete(public_path('uploads/projects/large/' . $oldImage));
                    File::delete(public_path('uploads/projects/small/' . $oldImage));
                }
            }
        }

            // Save updated project
            $project->save();
        
            return response()->json([
                'status' => true,
                'message' => 'Project updated successfully!',
                'data' => $project
            ]);
        }
        

    

    public function destroy($id){
        {
            $project = Project::find($id);
    
            // Check if service exists
            if (!$project) {
                return response()->json([
                    'status' => false,
                    'message' => 'Project not found'
                ], 404);
            }
    
            // Delete associated image files if present
            if ($project->image) {
                File::delete(public_path('uploads/projects/large/' . $project->image));
                File::delete(public_path('uploads/projects/small/' . $project->image));
            }
    
            // Delete the service record
            $project->delete();
    
            return response()->json([
                'status' => true,
                'message' => 'Project deleted successfully'
            ]);
        }
    }

}
