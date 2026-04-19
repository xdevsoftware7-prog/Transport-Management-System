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
        Schema::create('affectation_histos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chauffeur_id')->constrained();
            $table->foreignId('vehicule_id')->constrained();
            $table->dateTime('date_debut_conduite');
            $table->dateTime('date_fin_conduite')->nullable();
            $table->string('source')->nullable(); // ex: "véhicule actuel"
            $table->enum('statut', ['en_cours', 'terminé'])->default('en_cours');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affectation_histos');
    }
};
