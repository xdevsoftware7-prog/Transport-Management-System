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
        Schema::create('location_societes', function (Blueprint $table) {
            $table->id();
            $table->string('nom_societe');
            $table->string('telephone');
            $table->string('email')->nullable();
            $table->date('date_debut_contrat');
            $table->date('date_fin_contrat');
            $table->string('contrat_pdf_path')->nullable(); // Stockage du document
            $table->enum('statut', ['actif', 'en_attente', 'terminé'])->default('actif');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_societes');
    }
};
