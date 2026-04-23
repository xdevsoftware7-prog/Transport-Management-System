<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrimeDeplacement extends Model
{
    use HasFactory;

    protected $table = 'prime_deplacements';

    protected $fillable = [
        'trajet_id',
        'type_vehicule',
        'montant_prime',
    ];

    protected $casts = [
        'montant_prime' => 'decimal:2',
    ];

    /** Valeurs autorisées pour type_vehicule */
    public const TYPES_VEHICULE = [
        'tracteur',
        'semi-remorque',
        'camion',
        'fourgon',
        'benne',
        'citerne',
        'frigo',
        'plateau',
    ];

    public function trajet()
    {
        return $this->belongsTo(Trajet::class);
    }
}
