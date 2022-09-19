<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CourseController extends Controller
{
    public function index(){}

    public function store(Request $request){}

    public function update(Course $course, Request $request){
        $request->validate([
            "title" => "required|string"
        ]);

        try{
            $course->update([
                "title" =>  $request->title
            ]);

            return response()->json([
                "status" => "successful",
                "message" => "Course title has been successfully changed"
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                "status" => "failed",
                "message" => $e->getMessage()
            ]);
        }
    }
}
