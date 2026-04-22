<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SemiRemorqueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('semiRemorque')?->id;

        return [
            'matricule'     => ['required', 'string', 'max:50',  Rule::unique('semi_remorques', 'matricule')->ignore($id)],
            'marque'        => ['required', 'string', 'max:100'],
            'type_remorque' => ['required', 'string', 'max:100'],
            'ptac'          => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'vin'           => ['nullable', 'string', 'max:255', Rule::unique('semi_remorques', 'vin')->ignore($id)],
            'is_active'     => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'matricule'     => 'matricule',
            'marque'        => 'marque',
            'type_remorque' => 'type de remorque',
            'ptac'          => 'PTAC',
            'vin'           => 'numéro VIN',
            'is_active'     => 'statut actif',
        ];
    }

    public function messages(): array
    {
        return [
            'matricule.unique' => 'Ce matricule est déjà utilisé par une autre semi-remorque.',
            'vin.unique'       => 'Ce numéro VIN est déjà utilisé par une autre semi-remorque.',
            'ptac.numeric'     => 'Le PTAC doit être un nombre décimal.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'matricule' => strtoupper(trim($this->matricule ?? '')),
            'vin'       => $this->vin ? strtoupper(trim($this->vin)) : null,
        ]);
    }
}
