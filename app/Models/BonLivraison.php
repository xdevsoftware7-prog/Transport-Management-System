<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonLivraison extends Model
{
    protected $table = 'bon_livraisons';

    protected $fillable = [
        'num_bl',
        'commande_id',
        'vehicule_id',
        'chauffeur_id',
        'date_livraison_reelle',
        'statut',
    ];

    protected $casts = [
        'date_livraison_reelle' => 'datetime',
        'created_at'            => 'datetime',
        'updated_at'            => 'datetime',
    ];

    /* ── Constantes ──────────────────────────────────── */

    const STATUTS = [
        'brouillon'   => 'Brouillon',
        'émis'        => 'Émis',
        'livré'       => 'Livré',
        'partiel'     => 'Livraison partielle',
        'annulé'      => 'Annulé',
    ];

    /* ── Relations ───────────────────────────────────── */

    public function commande()
    {
        return $this->belongsTo(Commande::class, 'commande_id');
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class, 'vehicule_id');
    }

    public function chauffeur()
    {
        return $this->belongsTo(Chauffeur::class, 'chauffeur_id');
    }

    /* ── Accesseurs ──────────────────────────────────── */

    public function getStatutLabelAttribute(): string
    {
        return self::STATUTS[$this->statut] ?? (string) $this->statut ?: 'Inconnu';
    }

    public function getStatutColorAttribute(): string
    {
        return match ($this->statut) {
            'brouillon' => 'muted',
            'émis'      => 'blue',
            'livré'     => 'green',
            'partiel'   => 'orange',
            'annulé'    => 'red',
            default     => 'muted',
        };
    }
}
