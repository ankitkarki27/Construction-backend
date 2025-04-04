<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index(){
        //this method will return all active testimonials 
        $testimonials=Testimonial::where('status',1)
        ->orderBy('created_at','DESC')
        ->get();
        return response()->json([
            'status' => true,
            'data' => $testimonials
        ]);
       
    }


     // This method returns a single testimonial by ID
    //  public function testimonial($id){
    //     $testimonial = Testimonial::where('status', 1)
    //         ->where('id', $id)
    //         ->first();

    //     if (!$testimonial) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Testimonial not found'
    //         ], 404);
    //     }
    //     return response()->json([
    //         'status' => true,
    //         'data' => $testimonial
    //     ]);
    // }

    
    // public function testimonialBySlug($slug)
    // {
    // $testimonial = Testimonial::where('status', 1)
    //     ->where('slug', $slug)
    //     ->first();

    // if (!$testimonial) {
    //     return response()->json([
    //         'status' => false,
    //         'message' => 'Testimonial not found'
    //     ], 404);
    // }

    // return response()->json([
    //     'status' => true,
    //     'data' => $testimonial
    // ]);
    // }

    public function newtestimonials(Request $request){
        $testimonials = Testimonial::where('status', 1)
        ->orderBy('created_at', 'DESC')
        ->take($request->get('limit'))
        ->get();

        return response()->json([
            'status' => true,
            'data' => $testimonials
        ]);
    }


}
