<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGenreRequest extends FormRequest
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
        $genreId = $this->route('genre') ? ($this->route('genre')->id ?? $this->route('genre')) : null;

        return [
            'name' => [
                'required',
                'string',
                'max:45',
                Rule::unique('genres', 'name')->ignore($genreId),
            ],
        ];
    }
}
