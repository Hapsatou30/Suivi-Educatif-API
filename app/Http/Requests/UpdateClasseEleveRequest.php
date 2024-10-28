<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClasseEleveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
 
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'annee_classe_id' => 'required|exists:annee_classes,id', // Valider que l'ID existe dans la table annee_classes
        ];
    }
}
