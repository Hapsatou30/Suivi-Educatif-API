<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePresenceRequest extends FormRequest
{
  

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:present,absent',
            'justification' => 'nullable|string|max:255',
            'classe_eleve_id' => 'required|exists:classe_eleves,id',
            'classe_prof_id' => 'required|exists:classe_profs,id',
        ];
    }

    /**
     * Messages personnalisés pour chaque règle de validation.
     */
    public function messages(): array
    {
        return [
             'status.required' => 'Le statut de présence est obligatoire.',
            'status.in' => 'Le statut doit être soit "present" soit "absent".',
            'classe_eleve_id.required' => 'L\'identifiant de la classe élève est obligatoire.',
            'classe_eleve_id.exists' => 'La classe élève spécifiée n\'existe pas.',
            'classe_prof_id.required' => 'L\'identifiant de la classe prof est obligatoire.',
            'classe_prof_id.exists' => 'La classe prof spécifiée n\'existe pas.',
        ];
    }
}
