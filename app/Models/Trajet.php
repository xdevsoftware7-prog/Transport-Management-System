<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trajet extends Model
{
    use HasFactory;

    protected $fillable = ['ville_depart_id', 'ville_destination_id',    'adresse_depart',    'adresse_destination', 'distance_km', 'prix_autoroute', 'duree_minutes', 'statut'];

    public function villeDepart()
    {
        return $this->belongsTo(Ville::class, 'ville_depart_id');
    }
    public function villeDestination()
    {
        return $this->belongsTo(Ville::class, 'ville_destination_id');
    }
}
