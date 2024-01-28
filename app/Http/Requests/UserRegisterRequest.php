<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name" => "required",
            "email" => "required|email|unique:users",
            "password" => "required|confirmed",
            "cpf" => "required|max:20",
            "rg" => "max:20",
            "address" => "min:10|max:200",
            "addressNumber" => "max:10",
            "telephone" => "required|max:20",
            "postalCode" => "max:20",
            "salary" => "numeric"
        ];
    }
}
