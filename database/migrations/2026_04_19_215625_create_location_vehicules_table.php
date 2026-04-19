<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('location_vehicules', function (Blueprint $table) {
            $table->id();
            // Clés étrangères
            $table->foreignId('location_societe_id')->constrained('location_societes')->onDelete('cascade');
            $table->foreignId('vehicule_id')->constrained('vehicules')->onDelete('cascade');

            // Données spécifiques à l'état du véhicule lors du départ en location
            $table->decimal('prix_unitaire_ht', 10, 2);
            $table->integer('km_initial');
            $table->integer('niveau_carburant'); // 0 à 100
            $table->enum('etat_vehicule', ['excellent', 'bon', 'moyen', 'mauvais'])->default('excellent');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_vehicules');
    }
};
