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
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->string('num_facture')->unique();
            $table->foreignId('client_id')->constrained();
            $table->date('date_facture');
            $table->date('date_echeance');
            $table->decimal('total_ht', 15, 2);
            $table->decimal('total_tva', 15, 2);
            $table->enum('statut', ['réglée', 'non_réglée', 'en_retard'])->default('non_réglée');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
