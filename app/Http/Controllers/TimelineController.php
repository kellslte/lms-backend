<?php

namespace App\Http\Controllers;

use App\Models\Timeline;
use Illuminate\Http\Request;

class TimelineController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $timeline = collect(Timeline::all())->map(fn($timeline) => [
            "title" => $timeline->title,
            "done" => $timeline->done,
            "duration" => $timeline->duration(),
        ]);

        return response()->json([
            "status" => "success",
            "data" => [
                "timeline" => $timeline->toArray(),
            ]
        ]);
    }
}
