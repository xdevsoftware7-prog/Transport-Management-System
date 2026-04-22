<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chauffeur extends Model
{
    use HasFactory;
    protected $fillable = [
        'code_drv',
        'nom',
        'prenom',
        'telephone',
        'cin',
        'date_exp_cin',
        'cin_path',
        'date_exp_permis',
        'salaire_net',
        'salaire_brut',
        'statut',
    ];

    protected $casts = [
        'date_exp_cin'    => 'date',
        'date_exp_permis' => 'date',
        'salaire_net'     => 'decimal:2',
        'salaire_brut'    => 'decimal:2',
    ];

    // ── RELATIONS ──
    public function permis()
    {
        return $this->hasMany(ChauffeurPermis::class);
    }
}
