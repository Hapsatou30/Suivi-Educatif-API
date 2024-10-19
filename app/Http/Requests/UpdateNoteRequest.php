<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNoteRequest extends FormRequest
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
            'notes' => 'sometimes|numeric|min:0|max:20',
            'commentaire' => 'nullable|string|max:255',
            'evaluation_id' => 'sometimes|exists:evaluations,id',
            'bulletin_id' => 'sometimes|exists:bulletins,id',
            'periode' => 'sometimes|in:1_semestre,2_semestre', 
        ];
    }

    public function messages(): array
    {
        return [
            'notes.sometimes' => 'La note est obligatoire.',
            'notes.numeric' => 'La note doit être un nombre.',
            'notes.min' => 'La note doit être au moins 0.',
            'notes.max' => 'La note ne peut pas dépasser 20.',
            'commentaire.max' => 'Le commentaire ne peut pas dépasser 255 caractères.',
            'evaluation_id.sometimes' => 'L\'identifiant de l\'évaluation est obligatoire.',
            'evaluation_id.exists' => 'L\'évaluation spécifiée n\'existe pas.',
            'bulletin_id.sometimes' => 'L\'identifiant du bulletin est obligatoire.',
            'bulletin_id.exists' => 'Le bulletin spécifié n\'existe pas.',
            'periode.sometimes' => 'La période est obligatoire.',
            'periode.in' => 'La période doit être soit 1_semestre soit 2_semestre.',
        ];
    }
}
