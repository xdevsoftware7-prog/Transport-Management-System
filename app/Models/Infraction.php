<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Infraction extends Model
{
    protected $fillable = [
        'vehicule_id',
        'chauffeur_id',
        'date_infraction',
        'type_infraction',
        'montant',
        'description',
    ];

    protected $casts = [
        'date_infraction' => 'date',
        'montant'         => 'decimal:2',
    ];

    // ── Relations ──────────────────────────────────────────

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function chauffeur()
    {
        return $this->belongsTo(Chauffeur::class);
    }
}
