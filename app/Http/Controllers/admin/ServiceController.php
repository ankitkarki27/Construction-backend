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
    
    public function index()
    {
        $services= Service::orderBy('created_at','DESC')->get();
        return response()->json([
            'status'=>true,
            'data'=>$services
        ]);
    }


    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[

            'title'=>'required',
            'slug'=>'required|unique:services,slug',
        ]);

        // handling error case
        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }

        
        // service with no error
        $model=new Service();
        $model->title = $request-> title;
        $model->short_desc = $request-> short_desc;

        $model->slug = Str::slug($request->slug);
        $model->content = $request-> content;
        $model->status = $request-> status ?? 1 ; 
       $model->save();

    
       if($request->imageId>0){
        $serviceImage=ServiceImage::find($request->imageId);
        if($serviceImage !=null){

            $extArray= explode('.',$serviceImage->name);
            $ext=last($extArray);

            $fileName=strtotime('now').$model->id.'.'.$ext;

            
            //   create small thumbnail here
            $manager = new ImageManager(Driver::class);
            $sourcepath=public_path('uploads/servicetemp/'.$serviceImage->name);

            $destpath=public_path('uploads/services/small/'.$fileName);
            $image = $manager->read($sourcepath);
            $image->coverDown(375,400);
            $image->coverDown(500,600);
            $image->save($destpath);

              //   create large thumbnail here
         
              $destpath=public_path('uploads/services/large/'.$fileName);
              $image = $manager->read($sourcepath);
              $image->scaleDown(1200);
              $image->save($destpath);

              $model-> image=$fileName;
              $model->save();

            
        }
    }


       return response()->json([
        'status'=>true,
        'message'=>'Your Service added successfully'
    ]);
    }

    
    public function show( $id)
    {
        $service= Service::find($id);

        if($service == null){
            return response()->json([
                'status'=>false,
                'message'=>'Your Service not found'
            ]);
        }
        
       return response()->json([
        'status'=>true,
        'data'=>$service,
        // 'message'=>'Your Service updated successfully'
    ]);
    }

    
    public function edit(Service $service)
    {
        //
    }

    
    public function update(Request $request, $id)
    {
        $service= Service::find($id);
        if($service == null){
            return response()->json([
                'status'=>true,
                'message'=>'Your Service not found'
            ]);
        }

        $validator=Validator::make($request->all(),[
            'title'=>'required',
            'slug'=>'required|unique:services,slug,'.$id.',id'
        ]);

        // handling error case
        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }

          // Store old image before updating
        $oldImage = $service->image;

        // service with no error
        // $service=new Service();
        $service->title = $request-> title;
        $service->short_desc = $request-> short_desc;

        $service->slug = Str::slug($request->slug);
        $service->content = $request-> content;
        $service->status = $request-> status; 
        $service->save();

        // save service image here

        if($request->imageId>0){
            $serviceImage=ServiceImage::find($request->imageId);
            if($serviceImage !=null){

                $extArray= explode('.',$serviceImage->name);
                $ext=last($extArray);

                $fileName=strtotime('now').$service->id.'.'.$ext;

                
                //   create small thumbnail here
                $manager = new ImageManager(Driver::class);
                $sourcepath=public_path('uploads/servicetemp/'.$serviceImage->name);

                $destpath=public_path('uploads/services/small/'.$fileName);
                $image = $manager->read($sourcepath);
                $image->coverDown(375,400);
                $image->coverDown(500,600);
                $image->save($destpath);

                  //   create large thumbnail here
             
                  $destpath=public_path('uploads/services/large/'.$fileName);
                  $image = $manager->read($sourcepath);
                  $image->scaleDown(1200);
                  $image->save($destpath);

                  $service-> image=$fileName;
                  $service->save();

                  if($oldImage != ''){
                    File::delete(public_path('uploads/services/large/'.$oldImage)); 
                    File::delete(public_path('uploads/services/small/'.$oldImage)); 

                  }
                  
                
            }
        }

       return response()->json([
        'status'=>true,
        'message'=>'Your Service updated successfully'
    ]);
    }

    
    public function destroy($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'status' => false,
                'message' => 'Service not found'
            ], 404);
        }

        $service->delete();

        return response()->json([
            'status' => true,
            'message' => 'Service deleted successfully'
        ]);
    }
}
