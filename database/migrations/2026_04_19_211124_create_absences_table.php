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
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chauffeur_id')->constrained();
            $table->date('date_absence');
            $table->time('heure_entree')->nullable();
            $table->time('heure_sortie')->nullable();
            $table->decimal('heures_sup', 4, 2)->default(0);
            $table->string('motif')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};
