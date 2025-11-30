<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;

class PatchBookRequest extends FormRequest
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
            'title' => 'sometimes|string|max:255',
            'genres' => 'sometimes|array|min:1',
            'genres.*' => 'string|min:2',
            'stock' => 'sometimes|integer|min:0',
            'author' => 'sometimes|string',
            'description' => 'sometimes|string',
            'isbn' => 'sometimes|string',
        ];
    }

    public function messages(): array
    {
        return [

        ];
    }
}
