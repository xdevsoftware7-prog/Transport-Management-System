<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifClient extends Model
{
    use HasFactory;

    protected $table = 'tarif_clients';

    protected $fillable = [
        'client_id',
        'trajet_id',
        'type_vehicule',
        'tonnage',
        'prix_vente',
    ];

    protected $casts = [
        'tonnage'    => 'decimal:2',
        'prix_vente' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function trajet()
    {
        return $this->belongsTo(Trajet::class);
    }
}
