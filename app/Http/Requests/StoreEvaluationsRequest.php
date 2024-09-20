<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
class StoreEvaluationsRequest extends FormRequest
{
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'classe_prof_id' => ['required', 'exists:classe_profs,id'],
            'nom' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date', 'after_or_equal:' . Carbon::today()->toDateString()],
            'heure' => ['required', 'date_format:H:i'], 
            'duree' => ['required', 'integer', 'min:1'],
            'type_evaluation' => ['required', Rule::in(['Devoir', 'Examen'])],
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
            'classe_prof_id.required' => 'Le champ classe est obligatoire.',
            'classe_prof_id.exists' => 'La classe sélectionnée est invalide.',
            'nom.required' => 'Le nom de l\'évaluation est obligatoire.',
            'date.required' => 'La date de l\'évaluation est obligatoire.',
            'date.after_or_equal' => 'La date de l\'évaluation doit être aujourd\'hui ou une date future.',
            'heure.required' => 'L\'heure de l\'évaluation est obligatoire.',
            'heure.date_format' => 'L\'heure doit être au format HH:MM.',
            'duree.required' => 'La durée de l\'évaluation est obligatoire.',
            'duree.integer' => 'La durée doit être un entier.',
            'duree.min' => 'La durée doit être d\'au moins 1 minute.',
            'type_evaluation.required' => 'Le type d\'évaluation est obligatoire.',
            'type_evaluation.in' => 'Le type d\'évaluation doit être soit "Devoir" soit "Examen".',
        ];
    }
}
