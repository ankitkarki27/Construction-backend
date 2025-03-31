<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Testimonial;
use App\Models\ServiceImage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Support\Facades\Validator;
// use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Symfony\Contracts\Service\Attribute\Required;

class TestimonialController extends Controller
{
      // fetch all testimonials
    public function index(){
        // Fetch all testimonials (multiple records, so use plural)
        $testimonials = Testimonial::orderBy('created_at', 'DESC')
        ->get();

        return response()->json([
            'status' => true,
            'data' => $testimonials
        ]);
    }

    public function show($id)
    {
        // Fetch a single testimonial by ID (one record, so use singular)
        $testimonial = Testimonial::find($id);

        // not found->error message
        if (!$testimonial) {
            return response()->json([
                'status' => false,
                'message' => 'Testimonial not found'
            ]);
        }
        // found? -> return it in json response
        return response()->json([
            'status' => true,
            'data' => $testimonial
        ]);
    }



    public function store(Request $request)
    {

    // Validate input data
    $validator = Validator::make($request->all(), [
        'testimonial' => 'required|string|max:255',
        'name' => 'nullable|string|max:500',
        'designation' => 'nullable|string|max:500',
        'company' => 'nullable|string',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'status' => 'required|integer|in:0,1',
        'phone' => 'nullable|string|max:10',
    ]);

    // Store testimonial
    $testimonial = new Testimonial();
    $testimonial->name = $request->name;
    $testimonial->designation = $request->designation;
    $testimonial->company = $request->company;
    $testimonial->testimonial = $request->testimonial;
    $testimonial->status = $request->status;
    $testimonial->phone = $request->phone;

    $testimonial->save();

    if ($request->imageId > 0) {
        // ServiceImage model is for blog too. It contains code for blog also
        $serviceImage = ServiceImage::find($request->imageId);
        if ($serviceImage) {
            $extArray = explode('.', $serviceImage->name);
            $ext = last($extArray);
            $fileName = strtotime('now') .   $testimonial->id . '.' . $ext;

            $manager = new ImageManager(Driver::class);
            $sourcePath = public_path('uploads/servicetemp/' . $serviceImage->name);

            // Generate small thumbnail
            $smallPath = public_path('uploads/testimonials/small/' . $fileName);
            $image = $manager->read($sourcePath);
            $image->coverDown(375, 400);
            $image->save($smallPath);

            // Generate large thumbnail
            $largePath = public_path('uploads/blogs/large/' . $fileName);
            $image = $manager->read($sourcePath);
            $image->scaleDown(1200);
            $image->save($largePath);

            // Save image name in testimonial model
            $testimonial->image = $fileName;
            $testimonial->save();
    }

    // success? -> give success message
    return response()->json([
        'status'=>true,
        'message' => 'Testimonial created successfully!',
        'data' =>   $testimonial
    ]);
    }
    }

    public function update(Request $request, $id){
        // find testimonial by id
        $testimonial = Testimonial::find($id);
        if(!$testimonial){
            return response()->json([
                'status'=>false,
                'message'=>'Testimonial not found'
            ]);
        }

        // Validate Input data
        $validator=Validator::make($request->all(),[
           
            'name' => 'nullable|string|max:500',
            'designation' => 'nullable|string|max:500',
            'company' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|integer|in:0,1',
            'phone' => 'nullable|string|max:10',
        ]);

        if(!$validator->fails()){
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }

        // store old image before updatoing
        $oldImage=$testimonial->image;

        //update testimonial details
        $testimonial->name = $request->name;
        $testimonial->designation = $request->designation;
        $testimonial->company = $request->company;
        $testimonial->testimonial = $request->testimonial;
        $testimonial->status = $request->status;
        $testimonial->phone = $request->phone;
        $testimonial->save();

        //handle image update if provided
        if($request->imageId>0){

            $serviceImage = ServiceImage::find($request->imageId);
            if ($serviceImage) {
                $extArray = explode('.', $serviceImage->name);
                $ext = last($extArray);
                $fileName = strtotime('now') . $testimonial->id . '.' . $ext;

                $manager = new ImageManager(Driver::class);
                $sourcePath = public_path('uploads/servicetemp/' . $serviceImage->name);

                // Generate small thumbnail
                $destPath = public_path('uploads/testimonials/small/' . $fileName);
                $image = $manager->read($sourcePath);
                $image->coverDown(375, 400);
                // $image->coverDown(500, 600);
                $image->save($destPath);

                //save new image and delete old one
                $testimonial->image=$fileName;
                $testimonial->save();

                if($oldImage){
                    File::delete(public_path('uploads/testimonials/small/' . $oldImage));
                }
            }
        }
        // saved updated testimonial
        $testimonial->save();
        return response()->json([
            'status'=>true,
            'message'=>'Testimonial Updated Successfully',
            'data'=>$testimonial
        ]);
    }



    public function destroy($id)   {
        $testimonial = Testimonial::find($id);

        // Check if service exists
        if (!$testimonial) {
            return response()->json([
                'status' => false,
                'message' => 'Project not found'
            ], 404);
        }

        // Delete associated image files if present
        if ($testimonial->image) {
            // File::delete(public_path('uploads/projects/large/' . $testimonial->image));
            File::delete(public_path('uploads/testimonials/small/' . $testimonial->image));
        }

        // Delete the service record
        $testimonial->delete();

        return response()->json([
            'status' => true,
            'message' => 'testimonial deleted successfully'
        ]);
    }
}
