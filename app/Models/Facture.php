<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    use HasFactory;

    protected $table = 'factures';

    protected $fillable = [
        'num_facture',
        'client_id',
        'date_facture',
        'date_echeance',
        'total_ht',
        'total_tva',
        'statut',
    ];

    protected $casts = [
        'date_facture'  => 'date',
        'date_echeance' => 'date',
        'total_ht'      => 'decimal:2',
        'total_tva'     => 'decimal:2',
    ];

    // ── Constantes statut ──────────────────────────
    const STATUT_REGLEE     = 'réglée';
    const STATUT_NON_REGLEE = 'non_réglée';
    const STATUT_EN_RETARD  = 'en_retard';

    public static array $statuts = ['réglée', 'non_réglée', 'en_retard'];

    // ── Accessor : total TTC ───────────────────────
    public function getTotalTtcAttribute(): float
    {
        return (float) $this->total_ht + (float) $this->total_tva;
    }

    // ── Accessor : libellé statut lisible ─────────
    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            'réglée'     => 'Réglée',
            'non_réglée' => 'Non réglée',
            'en_retard'  => 'En retard',
            default      => $this->statut,
        };
    }

    // ── Accessor : classe CSS badge statut ────────
    public function getStatutBadgeClassAttribute(): string
    {
        return match ($this->statut) {
            'réglée'     => 'badge-success',
            'non_réglée' => 'badge-warning',
            'en_retard'  => 'badge-danger',
            default      => 'badge-secondary',
        };
    }

    // ── Relation ──────────────────────────────────
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
