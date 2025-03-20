<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class ServiceImageController extends Controller
{
  public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'image'=>'required|mimes:png,jpg,jpeg,gif'

        ]);
        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors('image')
            ]);
        }
  
  $image= $request->image;

  $ext =$image->getClientOriginalExtension();
  $imageName = strtotime('now') . '.' . $ext;  // e.g., 1742388877.jpg

  //save data in service images table
  $model = new ServiceImage();
  $model->name=$imageName;
  $model ->save();

  //save image in uploads/servicetemp directory
  $image->move(public_path('uploads/servicetemp'),$imageName);

//   create small thumbnail here

    $manager = new ImageManager(Driver::class);

        // image path making a variable
    $sourcepath=public_path('uploads/servicetemp/'.$imageName);

    $destpath=public_path('uploads/servicetemp/thumbnail/'.$imageName);
        // read image from file system
  
    $image = $manager->read($sourcepath);
    $image->coverDown(300,300);
    $image->save($destpath);
 
  return response()->json([
      'status'=>true,
      'data'=>$model,
      'message'=>'image uploaded successfully'
  ]);
}
     
}
