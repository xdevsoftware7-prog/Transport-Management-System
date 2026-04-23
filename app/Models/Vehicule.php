<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicule extends Model
{
    use HasFactory;

    protected $table = 'vehicules';

    protected $fillable = [
        'matricule',
        'marque',
        'type_vehicule',
        'acquisition',
        'date_circulation',
        'poids_a_vide',
        'ptac',
        'num_chassis',
        'km_initial',
        'statut',
        'chauffeur_id'
    ];

    protected $casts = [
        'date_circulation' => 'date',
        'poids_a_vide' => 'float',
        'ptac' => 'float',
        'km_initial' => 'integer'
    ];


 
    // ── RELATIONS ──

    /**
     * Le chauffeur affecté à ce véhicule (nullable).
     */
    public function chauffeur()
    {
        return $this->belongsTo(Chauffeur::class);
    }

    public function bonLivraisons()
    {
        return $this->hasMany(BonLivraison::class);
    }
}
