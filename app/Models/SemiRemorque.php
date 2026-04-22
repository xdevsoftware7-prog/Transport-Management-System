<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SemiRemorque extends Model
{
    use HasFactory;

    protected $fillable = [
        'matricule',
        'marque',
        'type_remorque',
        'ptac',
        'vin',
        'is_active'
    ];
}
