<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Mentor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StudentController extends Controller
{
    public function index()
    {
        $students = User::all();

        $mentors = Mentor::all();

        $response = $students->map(function (User $user) use ($mentors) {

            $mentor = $mentors->reject(function (Mentor $mentor) use ($user) {
                return !$mentor->mentees->where("student_id", $user->id);
            });

            $name = "";

            // if($mentor){
            //     $name = $mentor->name;
            // }

            return [
                'id' => $user->id,
                'name' => $user->name,
                'track' => $user->course->title,
                'points' => [
                    'attendance' => $user->point->attendance_points,
                    'tasks' => $user->point->task_points,
                    'bonus' => $user->point->bonus_points,
                    'total' => $user->point->total,
                ],
                'mentor' => $name
            ];
        })->groupBy('track');

        return response()->json([
            'status' => 'success',
            'data' => [
                'students' => $response->toArray()
            ]
        ]);
    }

    public function destroy(User $student)
    {
        try {
            $student->submissions()->delete();
            $student->schedule()->delete();
            $student->attendance()->delete();
            $student->point()->delete();
            $student->settings()->delete();
            $student->curriculum()->delete();
            $student->progress()->delete();
            $student->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Student record deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}