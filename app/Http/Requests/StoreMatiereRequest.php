<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMatiereRequest extends FormRequest
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
            'nom' => 'required|string|unique:matieres,nom|max:255',
            'description' => 'required|string|max:255',
            'coefficient' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le champ nom est obligatoire.',
            'nom.string' => 'Le champ nom doit être une chaîne de caractères.',
            'nom.unique' => 'Le nom de la matière existe déjà, il doit être unique.',
            'nom.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            
            'description.required' => 'Le champ description est obligatoire.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'description.max' => 'La description ne peut pas dépasser 255 caractères.',
            
            'coefficient.required' => 'Le champ coefficient est obligatoire.',
            'coefficient.integer' => 'Le coefficient doit être un nombre entier.',
            'coefficient.min' => 'Le coefficient doit être supérieur ou égal à 1.',
        ];
    }
}
