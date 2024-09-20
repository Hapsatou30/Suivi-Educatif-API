<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class UpdateCahierTexteRequest extends FormRequest
{
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'titre' => 'sometimes|string|max:255',
            'resume' => 'sometimes|string|max:255',
            'date' => ['sometimes', 'date', Rule::in([Carbon::today()->toDateString()])], 
            'ressource' => 'nullable|string',
            'classe_prof_id' => 'sometimes|exists:classe_profs,id', 
        ];
    }

    /**
     * Get the custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'titre.sometimes' => 'Le titre est requis.',
            'titre.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'resume.sometimes' => 'Le résumé est requis.',
            'resume.max' => 'Le résumé ne peut pas dépasser 255 caractères.',
            'date.sometimes' => 'La date est requise.',
            'date.date' => 'La date doit être une date valide.',
            'date.in' => 'La date doit être celle d\'aujourd\'hui.',
            'classe_prof_id.sometimes' => 'Le champ classe_prof_id est requis.',
            'classe_prof_id.exists' => 'La classe et le professeur spécifiés n\'existent pas.',
        ];
    }
}
