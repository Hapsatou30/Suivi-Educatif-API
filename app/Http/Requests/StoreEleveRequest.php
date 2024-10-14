<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEleveRequest extends FormRequest
{
    

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
           'date_naissance' => 'required|date|before:today|after_or_equal:' . now()->subYears(17)->toDateString() . '|before_or_equal:' . now()->subYears(11)->toDateString(),
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'genre' => 'required|in:Masculin,Feminin',
            'email' => 'nullable|email|unique:users,email', // Email de l'élève optionnel
            'parent_telephone' => 'required|string|regex:/^[0-9]{9}$/',
            'parent_nom' => 'required|string|max:255',
            'parent_prenom' => 'required|string|max:255',
            'parent_email' => 'required|email', // Email du parent obligatoire
            'parent_adresse' => 'required|string|max:255',
        ];
    }
    

    /**
     * Messages personnalisés pour chaque règle de validation.
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'date_naissance.required' => 'La date de naissance est obligatoire.',
            'date_naissance.date' => 'La date de naissance doit être une date valide.',
            'date_naissance.before' => 'La date de naissance doit être avant aujourd\'hui.',
            'date_naissance.after_or_equal' => 'L\'élève doit avoir au moins 11 ans.',
            'date_naissance.before_or_equal' => 'L\'élève ne doit pas avoir plus de 17 ans.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'telephone.regex' => 'Le numéro de téléphone doit comporter 10 chiffres.',
            'photo.image' => 'Le fichier téléchargé doit être une image.',
            'photo.mimes' => 'L\'image doit être au format jpeg, png, jpg, gif, ou svg.',
            'photo.max' => 'La taille maximale de l\'image est de 2 Mo.',
            'genre.required' => 'Le genre est obligatoire.',
            'genre.in' => 'Le genre doit être "Masculin" ou "Feminin".',
            'email.required' => 'L\'email est obligatoire.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'parent_telephone.required' => 'Le numéro de téléphone du parent est obligatoire.',
            'parent_telephone.regex' => 'Le numéro de téléphone du parent doit comporter 10 chiffres.',
            'parent_nom.required' => 'Le nom du parent est obligatoire.',
            'parent_prenom.required' => 'Le prénom du parent est obligatoire.',
            'parent_email.required' => 'L\'email du parent est obligatoire.',
            'parent_email.email' => 'L\'adresse email du parent doit être valide.',
            'parent_adresse.required' => 'L\'adresse du parent est obligatoire.',
        ];
    }
}
