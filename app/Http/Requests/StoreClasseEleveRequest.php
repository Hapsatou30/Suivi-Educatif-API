<?php

namespace App\Http\Requests;

use App\Models\AnneeClasse;
use Illuminate\Foundation\Http\FormRequest;

class StoreClasseEleveRequest extends FormRequest
{
   

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    { return [
        'annee_classe_id' => 'required|exists:annee_classes,id',
        'eleve_id' => 'required|exists:eleves,id',
    ];
}

/**
 * Messages d'erreurs personnalisés.
 */
public function messages(): array
{
    return [
        'annee_classe_id.required' => 'L\'année classe est obligatoire.',
        'annee_classe_id.exists' => 'L\'année classe spécifiée n\'existe pas.',
        'eleve_id.required' => 'L\'identifiant de l\'élève est obligatoire.',
        'eleve_id.exists' => 'L\'élève spécifié n\'existe pas.',
    ];
}
}
