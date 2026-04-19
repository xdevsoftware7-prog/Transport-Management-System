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
        Schema::create('vehicules', function (Blueprint $table) {
            $table->id();
            $table->string('matricule')->unique();
            $table->string('marque');
            $table->string('type_vehicule'); // Camion Benne, Tracteur, etc.
            $table->enum('acquisition', ['achat', 'location']);
            $table->date('date_circulation');
            $table->decimal('poids_a_vide', 8, 2);
            $table->decimal('ptac', 8, 2);
            $table->string('num_chassis')->unique();
            $table->integer('km_initial');
            $table->string('statut')->default('disponible');
            $table->foreignId('chauffeur_id')->nullable()->constrained('chauffeurs');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicules');
    }
};
