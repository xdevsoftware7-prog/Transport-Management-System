<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TrajetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ville_depart_id'      => ['required', 'exists:villes,id'],
            'ville_destination_id' => ['required', 'exists:villes,id', 'different:ville_depart_id'],
            'adresse_depart'       => ['nullable', 'string', 'max:500'],
            'adresse_destination'  => ['nullable', 'string', 'max:500'],
            'distance_km'          => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'prix_autoroute'       => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'duree_minutes'        => ['nullable', 'integer', 'min:0', 'max:99999'],
            'statut'               => ['required', Rule::in(['actif', 'inactif'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'ville_depart_id'      => 'ville de départ',
            'ville_destination_id' => 'ville de destination',
            'adresse_depart'       => 'adresse de départ',
            'adresse_destination'  => 'adresse de destination',
            'distance_km'          => 'distance (km)',
            'prix_autoroute'       => 'prix autoroute',
            'duree_minutes'        => 'durée (minutes)',
            'statut'               => 'statut',
        ];
    }

    public function messages(): array
    {
        return [
            'ville_destination_id.different' => 'La ville de destination doit être différente de la ville de départ.',
            'ville_depart_id.exists'         => 'La ville de départ sélectionnée est invalide.',
            'ville_destination_id.exists'    => 'La ville de destination sélectionnée est invalide.',
        ];
    }
}
