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

    public function index()
    {
         // To Fetch all projects
        $blogs = Blog::orderBy('created_at', 'DESC')->get();
        return response()->json([
            'status' => true,
            'data' =>  $blogs 
        ]);
    }

      public function show($id)
      {
        // find blog by id
          $blog = Blog::find($id);
        
          // Check if blog exists
          if (!$blog) {
              return response()->json([
                // $blog->increment('view_count');
                  'status' => false,
                  'message' => 'Blog not found'
              ]);
          }
  
          // Increment the view count if the blog exists
        $blog->increment('view_count');

          return response()->json([
              'status' => true,
              'data' => $blog
          ]);
      }
    

    public function store(Request $request)
    {

    $request->merge(['slug'=>Str::slug($request->slug)]);

    // Validate input data
    $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:blogs,slug',
        'author' => 'nullable|string|max:500',
        'short_desc' => 'nullable|string|max:500',
        'content' => 'nullable|string',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'status' => 'required|integer|in:0,1',
        // 'view_count' => 'required|integer|max:255',
    ]);

    // Store Blog
    $blog = new Blog();
    $blog->title = $request->title;
    $blog->slug = Str::slug($request->slug);
    $blog->short_desc = $request->short_desc;
    $blog->content = $request->content;
    $blog->author = $request->author;
    $blog->status = $request->status;
    $blog->view_count = 0; 

    $blog->save();

    if ($request->imageId > 0) {
        // ServiceImage model is for blog too.It contains code for blog also
        $serviceImage = ServiceImage::find($request->imageId);
        if ($serviceImage) {
            $extArray = explode('.', $serviceImage->name);
            $ext = last($extArray);
            $fileName = strtotime('now') . $blog->id . '.' . $ext;

            $manager = new ImageManager(Driver::class);
            $sourcePath = public_path('uploads/servicetemp/' . $serviceImage->name);

            // Generate small thumbnail
            $smallPath = public_path('uploads/blogs/small/' . $fileName);
            $image = $manager->read($sourcePath);
            $image->coverDown(375, 400);
            $image->save($smallPath);

            // Generate large thumbnail
            $largePath = public_path('uploads/blogs/large/' . $fileName);
            $image = $manager->read($sourcePath);
            $image->scaleDown(1200);
            $image->save($largePath);

            // Save image name in service model
            $blog->image = $fileName;
            $blog->save();
    }

    return response()->json([
        'status'=>true,
        'message' => 'Blog created successfully!',
        'data' => $blog
    ]);
    }
    }

    public function update(Request $request, $id)
    {
        // Find project by ID
        $blog = Blog::find($id);
    
        if (!$blog) {
            return response()->json([
                'status' => false,
                'message' => 'Blog not found'
            ], 404);
        }
    
        // Merge slug with a formatted version
        $request->merge(['slug' => Str::slug($request->slug)]);
    
        // Validate input data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            // 'slug' => 'required|string|max:255|unique:blogs,slug',
            'slug' => 'required|string|max:255|unique:blogs,slug,' . $blog->id,
            'author' => 'nullable|string|max:500',
            'short_desc' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|integer|in:0,1',
            // 'view_count' => 'required|integer|max:255',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
         // Store old image before updating
         $oldImage = $blog->image;

        // Update blog details
        $blog->title = $request->title;
        $blog->slug = Str::slug($request->slug);
        $blog->short_desc = $request->short_desc;
        $blog->content = $request->content;
        $blog->author = $request->author;
        $blog->status = $request->status;
        // $blog->view_count = 0; 
        $blog->save();

         // Handle image update if provided
    if ($request->imageId > 0) {
        $serviceImage = ServiceImage::find($request->imageId);
        if ($serviceImage) {
            $extArray = explode('.', $serviceImage->name);
            $ext = last($extArray);
            $fileName = strtotime('now') . $blog->id . '.' . $ext;

            $manager = new ImageManager(Driver::class);
            $sourcePath = public_path('uploads/servicetemp/' . $serviceImage->name);

            // Generate small thumbnail
            $destPath = public_path('uploads/blogs/small/' . $fileName);
            $image = $manager->read($sourcePath);
            $image->coverDown(375, 400);
            $image->save($destPath);

            // Generate large thumbnail
            $destPath = public_path('uploads/blogs/large/' . $fileName);
            $image = $manager->read($sourcePath);
            $image->scaleDown(1200);
            $image->save($destPath);

            // Save new image and delete old one
            $blog->image = $fileName;
            $blog->save();

            // Delete old image if it exists
            if ($oldImage) {
                File::delete(public_path('uploads/blogs/large/' . $oldImage));
                File::delete(public_path('uploads/blogs/small/' . $oldImage));
            }
        }
    }}
    
    public function destroy($id){
        {
            $blog = Blog::find($id);
    
            // Check if service exists
            if (!$blog) {
                return response()->json([
                    'status' => false,
                    'message' => 'Blog not found'
                ], 404);
            }
    
            // Delete associated image files if present
            if ($blog->image) {
                File::delete(public_path('uploads/blogs/large/' . $blog->image));
                File::delete(public_path('uploads/blogs/small/' . $blog->image));
            }
    
            // Delete the service record
            $blog->delete();
    
            return response()->json([
                'status' => true,
                'message' => 'Blog deleted successfully'
            ]);
        }
    }

}
