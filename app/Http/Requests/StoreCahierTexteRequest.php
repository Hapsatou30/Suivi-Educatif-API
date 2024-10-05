<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class StoreCahierTexteRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 'titre' => 'required|string|max:255',
            // 'resume' => 'required|string|max:255',
            // 'date' => ['required', 'date'], 
            // 'ressource' => 'nullable|string',
            // 'classe_prof_id' => 'required|exists:classe_profs,id', 
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
            'titre.required' => 'Le titre est requis.',
            'titre.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'resume.required' => 'Le résumé est requis.',
            'resume.max' => 'Le résumé ne peut pas dépasser 255 caractères.',
            'date.required' => 'La date est requise.',
            'date.date' => 'La date doit être une date valide.',
            'date.in' => 'La date doit être celle d\'aujourd\'hui.',
            'classe_prof_id.required' => 'Le champ classe_prof_id est requis.',
            'classe_prof_id.exists' => 'La classe et le professeur spécifiés n\'existent pas.',
        ];
    }
}
