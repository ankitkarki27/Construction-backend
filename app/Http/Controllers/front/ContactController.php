<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Mail\ContactEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function  index(Request $request){

        // 1.first validate request

        $validator = Validator::make($request-> all(),[
            'name'=>'required',
            'email'=>'required',

        ]);
        if($validator->fails()){

            return response()->json([
                'status'=> false,
                'errors'=>$validator->errors()

            ]);
        }
        // return response()->json([
        //     'status' => true,
        //     'data' =>  $request 
        // ]);

        $mailData=[
            'name'=>$request->name,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'subject'=>$request->subject,
            'message'=>$request->message,

        ];

        Mail:: to('admin.construction@gmail.com')->send(new ContactEmail($mailData));
        return response()->json([
            'status' => true,
            'message' =>  'Thank you for contacting us'
        ]);

    }
}
