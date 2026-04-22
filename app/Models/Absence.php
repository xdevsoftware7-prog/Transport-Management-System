<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;

    protected $fillable = [
        'chauffeur_id',
        'date_absence',
        'heure_entree',
        'heure_sortie',
        'heures_sup',
        'motif',
    ];

    protected $casts = [
        'date_absence' => 'date',
        'heures_sup'   => 'decimal:2',
    ];

    public function chauffeur()
    {
        return $this->belongsTo(Chauffeur::class);
    }
}
