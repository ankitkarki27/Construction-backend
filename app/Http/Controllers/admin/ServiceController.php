<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

   

    /**
     * Store a newly created resource in storage.
     */
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
        $model->status = $request-> status; 
       $model->save();

       return response()->json([
        'status'=>true,
        'message'=>'Your Service added successfully'
    ]);
    }

    
    public function show(Service $service)
    {
        //
    }

    
    public function edit(Service $service)
    {
        //
    }

    
    public function update(Request $request, Service $service)
    {
        //
    }

    
    public function destroy(Service $service)
    {
        //
    }
}
