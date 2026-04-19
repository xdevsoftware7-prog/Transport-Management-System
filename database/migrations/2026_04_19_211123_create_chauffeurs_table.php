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
        Schema::create('chauffeurs', function (Blueprint $table) {
            $table->id();
            $table->string('code_drv')->unique();
            $table->string('nom');
            $table->string('prenom');
            $table->string('telephone');
            $table->string('cin')->unique();
            $table->date('date_exp_cin');
            $table->string('cin_path')->nullable();
            $table->date('date_exp_permis');
            $table->decimal('salaire_net', 10, 2);
            $table->decimal('salaire_brut', 10, 2);
            $table->string('statut')->default('disponible'); // disponible, en voyage, absent
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chauffeurs');
    }
};
