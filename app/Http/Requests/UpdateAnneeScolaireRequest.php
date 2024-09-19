<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnneeScolaireRequest extends FormRequest
{
   

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         $annee_en_cours = date('Y');
        return [
            'annee_debut' => ['sometimes', 'digits:4', 'integer', 'gte:' . $annee_en_cours, 'max:' . ($annee_en_cours + 1)], 
            'annee_fin' => ['sometimes', 'digits:4', 'integer', 'gte:annee_debut', 'max:' . ($annee_en_cours + 2)], 
            'etat' => ['sometimes', 'in:En_cours,Fermée'], 
        ];
    }

    public function messages(): array
    {
        return [
            'annee_debut.sometimes' => "L'année de début est obligatoire.",
            'annee_debut.digits' => "L'année de début doit comporter 4 chiffres.",
            'annee_debut.integer' => "L'année de début doit être un nombre entier.",
            'annee_debut.gte' => "L'année de début ne peut pas être dans le passé. Veuillez entrer l'année en cours ou une année future.",
            'annee_debut.max' => "L'année de début ne peut pas dépasser l'année suivante.",

            'annee_fin.sometimes' => "L'année de fin est obligatoire.",
            'annee_fin.digits' => "L'année de fin doit comporter 4 chiffres.",
            'annee_fin.integer' => "L'année de fin doit être un nombre entier.",
            'annee_fin.gte' => "L'année de fin doit être supérieure ou égale à l'année de début.",
            'annee_fin.max' => "L'année de fin ne peut pas dépasser l'année dans deux ans.",

            'etat.sometimes' => "L'état est obligatoire.",
            'etat.in' => "L'état doit être soit 'En_cours' soit 'Fermée'.",
        ];
    }
}
