<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LocationSocieteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom_societe'         => ['required', 'string', 'max:255'],
            'telephone'           => ['nullable', 'string', 'max:30'],
            'email'               => ['nullable', 'email', 'max:255'],
            'date_debut_contrat'  => ['nullable', 'date'],
            'date_fin_contrat'    => ['nullable', 'date', 'after_or_equal:date_debut_contrat'],
            'contrat_pdf'         => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'statut'              => ['required', Rule::in(['actif', 'en_attente', 'terminé'])],
            'notes'               => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nom_societe'        => 'nom de la société',
            'telephone'          => 'téléphone',
            'email'              => 'adresse e-mail',
            'date_debut_contrat' => 'date de début du contrat',
            'date_fin_contrat'   => 'date de fin du contrat',
            'contrat_pdf'        => 'contrat PDF',
            'statut'             => 'statut',
            'notes'              => 'notes',
        ];
    }

    public function messages(): array
    {
        return [
            'date_fin_contrat.after_or_equal' => 'La date de fin doit être égale ou postérieure à la date de début.',
            'contrat_pdf.mimes'               => 'Le contrat doit être un fichier PDF.',
            'contrat_pdf.max'                 => 'Le fichier PDF ne doit pas dépasser 10 Mo.',
        ];
    }
}
