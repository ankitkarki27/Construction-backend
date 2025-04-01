<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\ServiceImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;

class TeamController extends Controller
{
    public function index()
    {
         // To Fetch all projects
        $teams = Team::orderBy('created_at', 'DESC')->get();
        return response()->json([
            'status' => true,
            'data' =>  $teams 
        ]);
    }

    // fetch a single team by ID
    public function show($id)
    {
        $team = Team::find($id);

        // Check if team exists
        if (!$team) {
            return response()->json([
                'status' => false,
                'message' => 'Team Member not found'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $team
        ]);
    }


    public function store(Request $request)
{
    // Ensure slug is generated automatically if not provided
    $request->merge(['slug' => Str::slug($request->name)]);

    // Validate request data
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:teams,slug',
        'company' => 'nullable|string|max:500',
        'designation' => 'nullable|string|max:500',
        'phone' => 'nullable|string|max:15',
        'email' => 'nullable|email|max:255',
        'bio' => 'nullable|string|max:5000',
        'status' => 'required|integer|in:0,1',
        'imageId' => 'nullable|integer',
    ]);

    // Return validation errors
    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors(),
        ], 400);
    }

    // Store team member
    $team = new Team();
    $team->name = $request->name;
    $team->slug = $request->slug;
    $team->company = $request->company;
    $team->designation = $request->designation;
    $team->phone = $request->phone;
    $team->email = $request->email;
    $team->bio = $request->bio;
    $team->status = $request->status;
    // $team->save();

    // Handle Image Upload if imageId exists
    if ($request->has('imageId') && $request->imageId > 0) {
        $serviceImage = ServiceImage::find($request->imageId);
           // If image is not found, return error response
           if (!$serviceImage) {
            return response()->json([
                'status' => false,
                'message' => 'The selected image does not exist in the database.',
            ], 400);
        }
        if ($serviceImage) {
            $extArray = explode('.', $serviceImage->name);
            $ext = last($extArray);
            $fileName = strtotime('now') . $team->id . '.' . $ext;

            $manager = new ImageManager(Driver::class);
            $sourcePath = public_path('uploads/servicetemp/' . $serviceImage->name);

            // Generate small thumbnail
            $smallPath = public_path('uploads/teams/small/' . $fileName);
            $image = $manager->read($sourcePath);
            $image->coverDown(375, 400);
            $image->save($smallPath);

            // Generate large thumbnail
            $largePath = public_path('uploads/teams/large/' . $fileName);
            $image = $manager->read($sourcePath);
            $image->scaleDown(1200);
            $image->save($largePath);

            // Save image name in the team model
            $team->image = $fileName;
            $team->save();
        }
    }

    $team->save();

    // Return success response
    return response()->json([
        'status' => true,
        'message' => 'Team member added successfully!',
        'data' => $team
    ]);
}

    
        public function update(Request $request, $id)
        {
            // Find project by ID
            $team = Team::find($id);
        
            if (!$team) {
                return response()->json([
                    'status' => false,
                    'message' => 'team not found'
                ], 404);
            }
        
            // Merge slug with a formatted version
            $request->merge(['slug' => Str::slug($request->slug)]);
        
            // Validate input data
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:teams,slug,' . $id,
                'company' => 'nullable|string|max:500',
                'designation' => 'nullable|string|max:500',
                'phone' => 'nullable|string|max:15',
                'email' => 'nullable|email|max:255',
                'bio' => 'nullable|string|max:5000',
                'status' => 'required|integer|in:0,1',
                'imageId' => 'nullable|integer',
            ]);
        
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ]);
            }
             // Store old image before updating
             $oldImage = $team->image;

            $team->name = $request->name;
            $team->slug = $request->slug;
            $team->company = $request->company;
            $team->designation = $request->designation;
            $team->phone = $request->phone;
            $team->email = $request->email;
            $team->bio = $request->bio;
            $team->status = $request->status;
            // $team->save();
          

             // Handle image update if provided
        if ($request->imageId > 0) {
            $serviceImage = ServiceImage::find($request->imageId);
            if ($serviceImage) {
                $extArray = explode('.', $serviceImage->name);
                $ext = last($extArray);
                $fileName = strtotime('now') . $team->id . '.' . $ext;

                $manager = new ImageManager(Driver::class);
                $sourcePath = public_path('uploads/servicetemp/' . $serviceImage->name);

                // Generate small thumbnail
                $destPath = public_path('uploads/teams/small/' . $fileName);
                $image = $manager->read($sourcePath);
                $image->coverDown(375, 400);
                // $image->coverDown(500, 600);
                $image->save($destPath);

                // Generate large thumbnail
                $destPath = public_path('uploads/teams/large/' . $fileName);
                $image = $manager->read($sourcePath);
                $image->scaleDown(1200);
                $image->save($destPath);

                // Save new image and delete old one
                $team->image = $fileName;
                $team->save();

                if ($oldImage) {
                    File::delete(public_path('uploads/teams/large/' . $oldImage));
                    File::delete(public_path('uploads/teams/small/' . $oldImage));
                }
            }
        }

            // Save updated project
            $team->save();
        
            return response()->json([
                'status' => true,
                'message' => 'Teams details updated successfully!',
                'data' => $team
            ]);
        }
        



public function destroy($id){
    {
        $team = Team::find($id);

        // Check if service exists
        if (!$team) {
            return response()->json([
                'status' => false,
                'message' => 'team member not found'
            ], 404);
        }

        // Delete associated image files if present
        if ($team->image) {
            File::delete(public_path('uploads/teams/large/' . $team->image));
            File::delete(public_path('uploads/teams/small/' . $team->image));
        }

        // Delete the service record
        $team->delete();

        return response()->json([
            'status' => true,
            'message' => 'Team member is  deleted successfully'
        ]);
    }
}




}
