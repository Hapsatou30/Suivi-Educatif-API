<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfMatiereRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'professeur_id' => 'required|exists:professeurs,id',
            'matiere_ids' => 'required|array',
            'matiere_ids.*' => 'exists:matieres,id',
        ];
    }

    public function messages(): array
    {
        return [
            'professeur_id.required' => 'L\'ID du professeur est requis.',
            'professeur_id.exists' => 'Le professeur spécifié n\'existe pas.',
            'matiere_ids.required' => 'Au moins une matière est requise.',
            'matiere_ids.array' => 'Les matières doivent être un tableau.',
            'matiere_ids.*.exists' => 'Une ou plusieurs matières spécifiées n\'existent pas.',
        ];
    }
}
