<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AunthenticationController extends Controller
{
    public function authenticate(Request $request){
        // Applying validation

        $validator = Validator::make($request->all(),[
            'email'=>'required|email',
            'password' =>'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
            
            }
            else{
                $credentials=[
                    'email'=>$request->email,
                    'password'=>$request->password,

                ];
                if(Auth::attempt($credentials)){
                    // Get authenticated user 
                    $user = User::find(Auth::user()->id);
                  
                    // get token
                    $token = $user->createToken('token')->plainTextToken;    

                    // return Auth::user();
                    return response()->json([
                        'status'=>true,
                        'token'=>$token,
                        'id'=>Auth::user()->id
                    ]); 
                        // end of access token

                }else{
                    return response()->json([
                        'status'=>false,
                        'message'=>'Either email or password is incorrect'
                    ]);   
                }
        }
    }

    public function logout(){
        $user = User::find(Auth::user()->id);
        $user->tokens()->delete();

        return response()->json([
            'status'=>true,
            'message'=>'Logout successfully'
        ]);   
    }
}
