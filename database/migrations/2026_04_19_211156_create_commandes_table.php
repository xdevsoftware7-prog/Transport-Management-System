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
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->string('code_commande')->unique();
            $table->foreignId('client_id')->constrained();
            $table->foreignId('trajet_id')->constrained();
            $table->dateTime('date_livraison');
            $table->enum('type', ['simple', 'groupé', 'composé']);
            $table->enum('statut', ['en_attente', 'planifiée', 'en_cours', 'exécutée'])->default('en_attente');
            $table->string('destinataire');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
