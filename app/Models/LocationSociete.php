<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationSociete extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_societe',
        'telephone',
        'email',
        'date_debut_contrat',
        'date_fin_contrat',
        'contrat_pdf',
        'statut',
        'notes'
    ];
    protected $casts = [
        'date_debut_contrat'    => 'date',
        'date_fin_contrat' => 'date',
    ];
}
