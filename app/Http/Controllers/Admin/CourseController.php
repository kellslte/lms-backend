<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CourseController extends Controller
{
    public function index(){
        return response()->json([
            "status" => "success",
            "data" => [
                "courses" => Course::all()
            ]
        ], 200);
    }

    public function store(Request $request){}

    public function update(String $course, Request $request){
        $dbname = Course::find($course);
        
        $request->validate([
            "title" => "required|string"
        ]);

        try{
            $dbname->update([
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

    public function destroy(Course $course){
        $course->delete();

        return response()->json([
            "status" => "success",
            "message" => "course has been deleted"
        ]);
    }
}
