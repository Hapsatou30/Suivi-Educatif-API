<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class UpdateEvaluationsRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
            return [
                'classe_prof_id' => ['sometimes', 'exists:classe_profs,id'],
                'nom' => ['sometimes', 'string', 'max:255'],
                'date' => ['sometimes', 'date', 'after_or_equal:' . Carbon::today()->toDateString()],
                'heure' => ['sometimes', 'date_format:H:i'], 
                'duree' => ['sometimes', 'integer', 'min:1'],
                'type_evaluation' => ['sometimes', Rule::in(['Devoir', 'Examen'])],
            ];
        }
    
        /**
         * Get custom error messages for validation failures.
         *
         * @return array<string, string>
         */
        public function messages(): array
        {
            return [
                'classe_prof_id.sometimes' => 'Le champ classe est obligatoire.',
                'classe_prof_id.exists' => 'La classe sélectionnée est invalide.',
                'nom.sometimes' => 'Le nom de l\'évaluation est obligatoire.',
                'date.sometimes' => 'La date de l\'évaluation est obligatoire.',
                'date.after_or_equal' => 'La date de l\'évaluation doit être aujourd\'hui ou une date future.',
                'heure.sometimes' => 'L\'heure de l\'évaluation est obligatoire.',
                'heure.date_format' => 'L\'heure doit être au format HH:MM.',
                'duree.sometimes' => 'La durée de l\'évaluation est obligatoire.',
                'duree.integer' => 'La durée doit être un entier.',
                'duree.min' => 'La durée doit être d\'au moins 1 minute.',
                'type_evaluation.sometimes' => 'Le type d\'évaluation est obligatoire.',
                'type_evaluation.in' => 'Le type d\'évaluation doit être soit "Devoir" soit "Examen".',
            ];
        
    }
}
