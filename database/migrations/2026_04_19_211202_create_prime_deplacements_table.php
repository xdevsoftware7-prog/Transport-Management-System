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
        Schema::create('prime_deplacements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trajet_id')->constrained();
            $table->string('type_vehicule');
            $table->decimal('montant_prime', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prime_deplacements');
    }
};
