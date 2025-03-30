<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Testimonial;
use App\Models\ServiceImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;

class TestimonialController extends Controller
{
    public function store(Request $request)
    {

    // $request->merge(['slug'=>Str::slug($request->slug)]);

    // Validate input data
    $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255',
        // 'slug' => 'required|string|max:255|unique:blogs,slug',
        'name' => 'nullable|string|max:500',
        'designation' => 'nullable|string|max:500',
        'company' => 'nullable|string',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'status' => 'required|integer|in:0,1',
        'phone' => 'nullable|string|max:10',
    ]);

    // Store Blog
    $testimonial = new Testimonial();
      // $testimonial->slug = Str::slug($request->slug);
    $testimonial->name = $request->name;
    $testimonial->designation = $request->designation;
    $testimonial->company = $request->company;
    $testimonial->testimonial = $request->testimonial;
    $testimonial->status = $request->status;
    $testimonial->phone = $request->phone;

    $testimonial->save();

    if ($request->imageId > 0) {
        // ServiceImage model is for blog too.It contains code for blog also
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

            // Save image name in service model
            $testimonial->image = $fileName;
            $testimonial->save();
    }

    return response()->json([
        'status'=>true,
        'message' => 'Testimonial created successfully!',
        'data' =>   $testimonial
    ]);
    }
    }
}
