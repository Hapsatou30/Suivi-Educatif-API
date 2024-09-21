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
            // 'nom' => 'required|string|max:255',
            // 'prenom' => 'required|string|max:255',
            // 'date_naissance' => 'required|date|before:today|before_or_equal:' . now()->subYears(5)->toDateString(),
            // 'telephone' => 'required|string|unique:eleves,telephone|regex:/^[0-9]{10}$/',
            // 'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
            // 'genre' => 'required|in:Masculin,Feminin',
            // 'user_id' => 'required|exists:users,id',
            // 'parent_id' => 'required|exists:parents,id',
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
            'date_naissance.before_or_equal' => 'L\'élève doit avoir au moins 5 ans.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'telephone.regex' => 'Le numéro de téléphone doit comporter 10 chiffres.',
            'photo.image' => 'Le fichier téléchargé doit être une image.',
            'photo.mimes' => 'L\'image doit être au format jpeg, png, jpg, gif, ou svg.',
            'photo.max' => 'La taille maximale de l\'image est de 2 Mo.',
            'genre.required' => 'Le genre est obligatoire.',
            'genre.in' => 'Le genre doit être "Masculin" ou "Feminin".',
            'user_id.required' => 'L\'identifiant de l\'utilisateur est obligatoire.',
            'user_id.exists' => 'L\'utilisateur spécifié n\'existe pas.',
            'parent_id.required' => 'L\'identifiant du parent est obligatoire.',
            'parent_id.exists' => 'Le parent spécifié n\'existe pas.',
        ];
    }
}
