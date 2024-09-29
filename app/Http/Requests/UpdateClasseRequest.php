<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClasseRequest extends FormRequest
{
    

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
       
        return [
            'nom' => 'sometimes|string|max:255' ,
            'niveau' => 'sometimes|string|max:255',
            'capacite' => 'sometimes|integer|min:1',
        ];
    }
    


    public function messages(): array
    {
        return [
            'nom.sometimes' => 'Le nom de la classe est obligatoire.',
            'nom.string' => 'Le nom de la classe doit être une chaîne de caractères.',
            'nom.unique' => 'Le nom de la classe existe déjà.',
            'nom.max' => 'Le nom de la classe ne doit pas dépasser 255 caractères.',
            
            'niveau.sometimes' => 'Le niveau de la classe est obligatoire.',
            'niveau.string' => 'Le niveau doit être une chaîne de caractères.',
            'niveau.max' => 'Le niveau ne doit pas dépasser 255 caractères.',
            
            'capacite.sometimes' => 'La capacité de la classe est obligatoire.',
            'capacite.integer' => 'La capacité doit être un nombre entier.',
            'capacite.min' => 'La capacité doit être au moins 1.',
        ];
    }
}
