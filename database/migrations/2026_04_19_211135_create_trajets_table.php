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
        Schema::create('trajets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ville_depart_id')->constrained('villes');
            $table->foreignId('ville_destination_id')->constrained('villes');
            $table->text('adresse_depart')->nullable();
            $table->text('adresse_destination')->nullable();
            $table->decimal('distance_km', 8, 2);
            $table->decimal('prix_autoroute', 10, 2)->default(0);
            $table->integer('duree_minutes');
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trajets');
    }
};
