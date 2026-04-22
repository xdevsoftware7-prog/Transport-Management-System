<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChauffeurPermis extends Model
{
    use HasFactory;
    protected $fillable = ['chauffeur_id', 'categorie'];
}
