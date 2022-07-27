<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'fullname' => 'required|string',
            'email' => 'required|email',
            'gender' => 'required|string',
            'phonenumber' => 'required|string',
            'accessToLaptop' => 'nullable|string',
            'currentEducationLevel' => 'nullable|string',
            'githubLink' => 'nullable|string',
            'cvDetails' => 'nullable|string',
            'trackId' => 'required|numeric',
            'courseId' => 'required|numeric'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]));
    }
}
