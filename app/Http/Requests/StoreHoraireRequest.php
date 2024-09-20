<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class StoreHoraireRequest extends FormRequest
{
   

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'jour' => [
                'required',
                'string',
                Rule::in(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi']),
            ],
            'heure_debut' => 'required|date_format:H:i|before:heure_fin',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'classe_prof_id' => 'required|exists:classe_profs,id',
        ];
    }

    /**
     * Get the custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'jour.required' => 'Le jour est obligatoire.',
            'jour.string' => 'Le jour doit être une chaîne de caractères valide.',
            'jour.in' => 'Le jour doit être un des jours suivants : lundi, mardi, mercredi, jeudi, vendredi, samedi, dimanche.',
            'heure_debut.required' => 'L\'heure de début est obligatoire.',
            'heure_debut.date_format' => 'L\'heure de début doit être au format HH:mm.',
            'heure_debut.before' => 'L\'heure de début doit être avant l\'heure de fin.',
            'heure_fin.required' => 'L\'heure de fin est obligatoire.',
            'heure_fin.date_format' => 'L\'heure de fin doit être au format HH:mm.',
            'heure_fin.after' => 'L\'heure de fin doit être après l\'heure de début.',
            'classe_prof_id.required' => 'L\'ID de la classe et du professeur est obligatoire.',
            'classe_prof_id.exists' => 'L\'ID de la classe et du professeur doit exister dans la base de données.',
        ];
    }
}
