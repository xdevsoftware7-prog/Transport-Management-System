<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicule_id',
        'type_intervention',
        'cout_total',
        'statut',
        'date_debut',
        'date_fin',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin'   => 'date',
        'cout_total' => 'decimal:2',
    ];

    // ── Constantes statuts ──────────────────────────────────────
    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_EN_COURS   = 'en_cours';
    const STATUT_TERMINEE   = 'terminée';

    public static function statuts(): array
    {
        return [
            self::STATUT_EN_ATTENTE => 'En attente',
            self::STATUT_EN_COURS   => 'En cours',
            self::STATUT_TERMINEE   => 'Terminée',
        ];
    }

    // ── Relations ───────────────────────────────────────────────
    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    // ── Accessors ───────────────────────────────────────────────
    public function getStatutLabelAttribute(): string
    {
        return self::statuts()[$this->statut] ?? $this->statut;
    }

    public function getStatutColorAttribute(): string
    {
        return match ($this->statut) {
            self::STATUT_EN_ATTENTE => 'warning',
            self::STATUT_EN_COURS   => 'info',
            self::STATUT_TERMINEE   => 'success',
            default                 => 'muted',
        };
    }
}
