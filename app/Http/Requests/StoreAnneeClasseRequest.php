<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnneeClasseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'annee_id' => 'required|exists:annee_scolaires,id',
            'classe_ids' => 'required|array',
            'classe_ids.*' => 'exists:classes,id',
        ];
    }

    public function messages(): array
    {
        return [
            'annee_id.required' => 'L\'ID de l\'année est requis.',
            'annee_id.exists' => 'L\'année spécifiée n\'existe pas.',
            'classe_ids.required' => 'Au moins une classe est requise.',
            'classe_ids.array' => 'Les classes doivent être un tableau.',
            'classe_ids.*.exists' => 'Une ou plusieurs classes spécifiées n\'existent pas.',
        ];
    }
}
