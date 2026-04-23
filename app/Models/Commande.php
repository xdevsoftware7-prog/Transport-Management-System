<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    // ── Table & clé primaire ───────────────────────────────────────────────
    protected $table = 'commandes';

    // ── Champs mass-assignable ─────────────────────────────────────────────
    protected $fillable = [
        'code_commande',
        'client_id',
        'trajet_id',
        'date_livraison',
        'type',
        'statut',
        'destinataire',
    ];

    // ── Casts ──────────────────────────────────────────────────────────────
    protected $casts = [
        'date_livraison' => 'date',
        'client_id'      => 'integer',
        'trajet_id'      => 'integer',
    ];

    // ── Valeurs par défaut ─────────────────────────────────────────────────
    protected $attributes = [
        'statut' => 'en_attente',
    ];
 
    // ════════════════════════════════════════════════════════════════════════
    // RELATIONS
    // ════════════════════════════════════════════════════════════════════════

    /**
     * Client donneur d'ordre
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Trajet associé
     */
    public function trajet()
    {
        return $this->belongsTo(Trajet::class);
    }

    // ════════════════════════════════════════════════════════════════════════
    // SCOPES
    // ════════════════════════════════════════════════════════════════════════

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    public function scopeLivrees($query)
    {
        return $query->where('statut', 'livree');
    }

    public function scopeAnnulees($query)
    {
        return $query->where('statut', 'annulee');
    }
 
    // ════════════════════════════════════════════════════════════════════════
    // ACCESSEURS
    // ════════════════════════════════════════════════════════════════════════

    /**
     * Libellé lisible du statut
     */
    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            'en_attente' => 'En attente',
            'en_cours'   => 'En cours',
            'livree'     => 'Livrée',
            'annulee'    => 'Annulée',
            default      => ucfirst($this->statut),
        };
    }

    /**
     * Libellé lisible du type
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'import'  => 'Import',
            'export'  => 'Export',
            'local'   => 'Local',
            'transit' => 'Transit',
            default   => ucfirst($this->type ?? ''),
        };
    }
}
