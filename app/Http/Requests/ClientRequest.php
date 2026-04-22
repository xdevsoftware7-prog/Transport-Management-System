<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $clientId = $this->route('client')?->id;

        return [
            'nom'                 => ['required', 'string', 'max:255'],
            'type'                => ['required', Rule::in(['entreprise', 'particulier'])],
            'email'               => ['nullable', 'email', 'max:255', Rule::unique('clients', 'email')->ignore($clientId)],
            'telephone'           => ['nullable', 'string', 'max:30'],
            'adresse'             => ['nullable', 'string', 'max:500'],
            'ice'                 => ['nullable', 'string', 'max:50', Rule::unique('clients', 'ice')->ignore($clientId)],
            'identifiant_fiscal'  => ['nullable', 'string', 'max:50'],
            'registre_commerce'   => ['nullable', 'string', 'max:50'],
            'statut_juridique'    => ['nullable', 'string', 'max:100'],
            'patente'             => ['nullable', 'string', 'max:50'],
            'num_cnss'            => ['nullable', 'string', 'max:50'],
            'modalite_paiement'   => ['required', Rule::in(['comptant', '30_jours', '60_jours'])],
            'is_active'           => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nom'                => 'nom du client',
            'type'               => 'type de client',
            'email'              => 'adresse e-mail',
            'telephone'          => 'téléphone',
            'adresse'            => 'adresse',
            'ice'                => 'ICE',
            'identifiant_fiscal' => 'identifiant fiscal',
            'registre_commerce'  => 'registre de commerce',
            'statut_juridique'   => 'statut juridique',
            'patente'            => 'patente',
            'num_cnss'           => 'numéro CNSS',
            'modalite_paiement'  => 'modalité de paiement',
            'is_active'          => 'statut actif',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
