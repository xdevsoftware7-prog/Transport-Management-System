<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trajet extends Model
{
    use HasFactory;

    protected $fillable = ['ville_depart_id', 'ville_destination_id',    'adresse_depart',    'adresse_destination', 'distance_km', 'prix_autoroute', 'duree_minutes', 'statut'];

    protected $casts = [
        'distance_km'    => 'decimal:2',
        'prix_autoroute' => 'decimal:2',
        'duree_minutes'  => 'integer',
    ];

    public function villeDepart()
    {
        return $this->belongsTo(Ville::class, 'ville_depart_id');
    }
    public function villeDestination()
    {
        return $this->belongsTo(Ville::class, 'ville_destination_id');
    }

    public function tarifClients()
    {
        return $this->hasMany(TarifClient::class);
    }
    public function primeDeplacements()
    {
        return $this->hasMany(PrimeDeplacement::class);
    }
    // Accessor pratique pour afficher le libellé du trajet
    public function getLabelAttribute(): string
    {
        $dep  = $this->villeDepart?->nom  ?? '?';
        $dest = $this->villeDestination?->nom ?? '?';
        return "{$dep} → {$dest}";
    }
}
