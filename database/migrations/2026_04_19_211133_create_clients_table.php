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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->enum('type', ['entreprise', 'particulier']);
            $table->string('email')->nullable();
            $table->string('telephone');
            $table->text('adresse');
            $table->string('ice')->nullable();
            $table->string('identifiant_fiscal')->nullable();
            $table->string('registre_commerce')->nullable();
            $table->string('statut_juridique')->nullable();
            $table->string('patente')->nullable();
            $table->string('num_cnss')->nullable();
            $table->enum('modalite_paiement', ['comptant', '30_jours', '60_jours'])->default('comptant');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
