<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMatiereRequest extends FormRequest
{
   

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => 'sometimes|string|max:255|unique:matieres,nom,' . $this->matiere->id,
            'description' => 'sometimes|string|max:255',
            'coefficient' => 'sometimes|integer|min:1',
        ];
    }
    

    public function messages(): array
    {
        return [
            'nom.sometimes' => 'Le champ nom est obligatoire.',
            'nom.string' => 'Le champ nom doit être une chaîne de caractères.',
            'nom.unique' => 'Le nom de la matière existe déjà, il doit être unique.',
            'nom.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            
            'description.sometimes' => 'Le champ description est obligatoire.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'description.max' => 'La description ne peut pas dépasser 255 caractères.',
            
            'coefficient.sometimes' => 'Le champ coefficient est obligatoire.',
            'coefficient.integer' => 'Le coefficient doit être un nombre entier.',
            'coefficient.min' => 'Le coefficient doit être supérieur ou égal à 1.',
        ];
    }
}
