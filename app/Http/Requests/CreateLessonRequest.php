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
            'status' => 'required|string',
            'courseTitle' => 'required|string',
            'lessonVideo' => 'required|file|mime:mp4,avi,mkv',
            'lessonThumbnail' => 'required|file|mime:png',
            'lessonTransacript' => 'required|file|mime:txt'
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
