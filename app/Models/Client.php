<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $fillable = [
        'nom',
        'type',
        'email',
        'telephone',
        'adresse',
        'ice',
        'identifiant_fiscal',
        'registre_commerce',
        'statut_juridique',
        'patente',
        'num_cnss',
        'modalite_paiement',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
    public function tarifClients()
    {
        return $this->hasMany(TarifClient::class);
    }
    public function commandes()
    {
        return $this->hasMany(Commande::class);
    }
}
