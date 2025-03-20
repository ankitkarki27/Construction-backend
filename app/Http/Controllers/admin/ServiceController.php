<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ServiceController extends Controller
{
    // Fetch all services, ordered by latest creation date
    public function index()
    {
        $services = Service::orderBy('created_at', 'DESC')->get();
        return response()->json([
            'status' => true,
            'data' => $services
        ]);
    }

    // Store a new service in the database
    public function store(Request $request)
    {
        // Validate request inputs
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'slug' => 'required|unique:services,slug',
        ]);

        // Return errors if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        // Create new service instance
        $model = new Service();
        $model->title = $request->title;
        $model->short_desc = $request->short_desc;
        $model->slug = Str::slug($request->slug);
        $model->content = $request->content;
        $model->status = $request->status ?? 1;
        $model->save();

        // Handle service image if provided
        if ($request->imageId > 0) {
            $serviceImage = ServiceImage::find($request->imageId);
            if ($serviceImage) {
                $extArray = explode('.', $serviceImage->name);
                $ext = last($extArray);
                $fileName = strtotime('now') . $model->id . '.' . $ext;

                $manager = new ImageManager(Driver::class);
                $sourcePath = public_path('uploads/servicetemp/' . $serviceImage->name);

                // Generate small thumbnail
                $destPath = public_path('uploads/services/small/' . $fileName);
                $image = $manager->read($sourcePath);
                $image->coverDown(375, 400);
                $image->coverDown(500, 600);
                $image->save($destPath);

                // Generate large thumbnail
                $destPath = public_path('uploads/services/large/' . $fileName);
                $image = $manager->read($sourcePath);
                $image->scaleDown(1200);
                $image->save($destPath);

                // Save image name in service model
                $model->image = $fileName;
                $model->save();
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Your Service added successfully'
        ]);
    }

    // fetch a single service by ID
    public function show($id)
    {
        $service = Service::find($id);

        // Check if service exists
        if (!$service) {
            return response()->json([
                'status' => false,
                'message' => 'Service not found'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $service
        ]);
    }

    // Update an existing service
    public function update(Request $request, $id)
    {
        $service = Service::find($id);

        // Check if service exists
        if (!$service) {
            return response()->json([
                'status' => false,
                'message' => 'Service not found'
            ]);
        }

        // Validate request inputs
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'slug' => 'required|unique:services,slug,' . $id . ',id'
        ]);

        // Return errors if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        // Store old image before updating
        $oldImage = $service->image;

        // Update service fields
        $service->title = $request->title;
        $service->short_desc = $request->short_desc;
        $service->slug = Str::slug($request->slug);
        $service->content = $request->content;
        $service->status = $request->status;
        $service->save();

        // Handle image update if provided
        if ($request->imageId > 0) {
            $serviceImage = ServiceImage::find($request->imageId);
            if ($serviceImage) {
                $extArray = explode('.', $serviceImage->name);
                $ext = last($extArray);
                $fileName = strtotime('now') . $service->id . '.' . $ext;

                $manager = new ImageManager(Driver::class);
                $sourcePath = public_path('uploads/servicetemp/' . $serviceImage->name);

                // Generate small thumbnail
                $destPath = public_path('uploads/services/small/' . $fileName);
                $image = $manager->read($sourcePath);
                $image->coverDown(375, 400);
                $image->coverDown(500, 600);
                $image->save($destPath);

                // Generate large thumbnail
                $destPath = public_path('uploads/services/large/' . $fileName);
                $image = $manager->read($sourcePath);
                $image->scaleDown(1200);
                $image->save($destPath);

                // Save new image and delete old one
                $service->image = $fileName;
                $service->save();

                if ($oldImage) {
                    File::delete(public_path('uploads/services/large/' . $oldImage));
                    File::delete(public_path('uploads/services/small/' . $oldImage));
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Your Service updated successfully'
        ]);
    }

    // Delete a service by ID
    public function destroy($id)
    {
        $service = Service::find($id);

        // Check if service exists
        if (!$service) {
            return response()->json([
                'status' => false,
                'message' => 'Service not found'
            ], 404);
        }

        // Delete associated image files if present
        if ($service->image) {
            File::delete(public_path('uploads/services/large/' . $service->image));
            File::delete(public_path('uploads/services/small/' . $service->image));
        }

        // Delete the service record
        $service->delete();

        return response()->json([
            'status' => true,
            'message' => 'Service deleted successfully'
        ]);
    }
}
