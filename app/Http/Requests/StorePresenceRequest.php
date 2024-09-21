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
            'date_presence' => 'required|date',
            'status' => 'required|in:present,absent',
            'motif' => 'required_if:status,absent|string|max:255',
            'justification' => 'nullable|required_if:status,absent|string|max:255',
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
            'date_presence.required' => 'La date de présence est obligatoire.',
            'date_presence.date' => 'La date de présence doit être une date valide.',
            'status.required' => 'Le statut de présence est obligatoire.',
            'status.in' => 'Le statut doit être soit "present" soit "absent".',
            'motif.required_if' => 'Le motif est obligatoire si l\'élève est absent.',
            'justification.required_if' => 'La justification est obligatoire si l\'élève est absent.',
            'classe_eleve_id.required' => 'L\'identifiant de la classe élève est obligatoire.',
            'classe_eleve_id.exists' => 'La classe élève spécifiée n\'existe pas.',
            'classe_prof_id.required' => 'L\'identifiant de la classe prof est obligatoire.',
            'classe_prof_id.exists' => 'La classe prof spécifiée n\'existe pas.',
        ];
    }
}
