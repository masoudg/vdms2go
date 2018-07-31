<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

abstract class FormRequest extends Request
{
    public function validate()
    {
        $validator = app('validator')->make($this->all(), $this->rules(), $this->messages());
        
        if ($validator->fails()) {
            throw new ValidationException($validator->errors()->first());
        }
    }

    abstract protected function rules();
    
    abstract protected function messages();
}
