<?php

namespace App\Http\Requests;

use App\Models\Professeur;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfesseurRequest extends FormRequest
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
            'nom' => 'nullable|string|max:255',   
            'prenom' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
    

    public function messages(): array
    {
        return [
            'email.sometimes' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'email.max' => 'L\'email ne doit pas dépasser 255 caractères.',
            
            'nom.sometimes' => 'Le nom est obligatoire.',
            'nom.string' => 'Le nom doit être une chaîne de caractères.',
            'nom.max' => 'Le nom ne doit pas dépasser 255 caractères.',

            'prenom.sometimes' => 'Le prénom est obligatoire.',
            'prenom.string' => 'Le prénom doit être une chaîne de caractères.',
            'prenom.max' => 'Le prénom ne doit pas dépasser 255 caractères.',

            'telephone.sometimes' => 'Le téléphone est obligatoire.',
            'telephone.string' => 'Le téléphone doit être une chaîne de caractères.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'telephone.max' => 'Le téléphone ne doit pas dépasser 15 caractères.',
            'telephone.regex' => 'Le numéro de téléphone doit être au format valide.',

            'photo.image' => 'Le fichier doit être une image.',
            'photo.mimes' => 'Les formats autorisés sont jpeg, png, jpg, gif.',
            'photo.max' => 'La photo ne doit pas dépasser 2 Mo.',

           ];
    }
}
