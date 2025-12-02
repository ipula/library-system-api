<?php

namespace App\Interfaces\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'genres' => 'required|array|min:1',
            'genres.*' => 'string|min:2',
            'stock' => 'required|integer|min:0',
            'author' => 'required|string',
            'description' => 'sometimes|string',
            'isbn' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [

        ];
    }
}
