<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateLessonRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'required|string|min:3',
            'description' => 'required|min:8',
            'status' => 'nullable|string',
            'lessonVideo' => 'required|file|mimes:mp4,avi,mkv',
            'lessonThumbnail' => 'required|file|mimes:png,jpg,jpeg',
            'lessonTranscript' => 'nullable|file|mimes:txt',
            'resources.*' => "required|string|min:1",
            'courseTitle' => 'nullable|string'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ], 400));
    }
}
