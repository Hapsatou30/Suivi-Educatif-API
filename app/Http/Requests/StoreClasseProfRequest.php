<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClasseProfRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
  

    /**
     * Obtenir les règles de validation qui s'appliquent à la requête.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'annee_classe_id' => 'required|exists:annee_classes,id',
            'prof_mat_ids' => 'required|array',
            'prof_mat_ids.*' => 'exists:prof_matieres,id',
        ];
    }

    public function messages(): array
    {
        return [
            'annee_classe_id.required' => 'L\'ID de l\'année classe est requis.',
            'annee_classe_id.exists' => 'L\'année classe spécifiée n\'existe pas.',
            'prof_mat_ids.required' => 'Au moins une matière-professeur est requise.',
            'prof_mat_ids.array' => 'Les matières-professeurs doivent être un tableau.',
            'prof_mat_ids.*.exists' => 'Une ou plusieurs matières-professeurs spécifiées n\'existent pas.',
        ];
    }
}
