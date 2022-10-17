<?php
namespace App\Actions;



use Illuminate\Http\Request;

class CreateLesson {
    public function handle(Request $request){
        // try upload videos to YouTube
        try {
            $response = UploadLesson::handle($request);

        }catch(\Exception $e){

        }

        // if upload to YouTube fails, then upload to server

        // try to create lesson then

        // return lesson
    }
}
