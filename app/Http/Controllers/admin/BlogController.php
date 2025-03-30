<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\ServiceImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;

class BlogController extends Controller
{
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

    // $table->id(); 
    // $table->string('title'); 
    // $table->string('slug')->unique();
    // $table->string('author')->nullable(); 
    // $table->string('short_desc')->nullable(); 
    // $table->text('content')->nullable(); 
    // $table->string('image')->nullable(); 
    // $table->integer('status')->default(1); 
    // $table->integer('view_count')->default(0); 
    // $table->timestamps(); 

    // Store project
    $blog = new Blog();
    $blog->title = $request->title;
    // $project->slug = $request->slug;
    $blog->slug = Str::slug($request->slug);
    $blog->short_desc = $request->short_desc;
    $blog->content = $request->content;
    $blog->construction_type = $request->construction_type;
    $blog->sector = $request->sector;
    $blog->location = $request->location;
    $blog->status = $request->status;

    $blog->save();

    if ($request->imageId > 0) {
        // ServiceImage model is for project too.It contains code for ptoject also
        $serviceImage = ServiceImage::find($request->imageId);
        if ($serviceImage) {
            $extArray = explode('.', $serviceImage->name);
            $ext = last($extArray);
            $fileName = strtotime('now') . $blog->id . '.' . $ext;

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
            $blog->image = $fileName;
            $blog->save();
    }

    return response()->json([
        'status'=>true,
        'message' => 'Project created successfully!',
        'data' => $blog
    ]);
    }
    }
}
