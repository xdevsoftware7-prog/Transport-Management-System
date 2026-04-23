<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LigneCommande extends Model
{
    protected $fillable = [
        'commande_id',
        'article_id',
        'quantite',
        'poids_kg',
    ];

    protected $casts = [
        'quantite'  => 'integer',
        'poids_kg'  => 'decimal:3',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ── Relations ───────────────────────────────────── */

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
