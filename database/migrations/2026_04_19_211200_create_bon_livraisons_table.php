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
        Schema::create('bon_livraisons', function (Blueprint $table) {
            $table->id();
            $table->string('num_bl')->unique();
            $table->foreignId('commande_id')->constrained();
            $table->foreignId('vehicule_id')->constrained();
            $table->foreignId('chauffeur_id')->constrained();
            $table->dateTime('date_livraison_reelle');
            $table->string('statut')->default('livré');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bon_livraisons');
    }
};
