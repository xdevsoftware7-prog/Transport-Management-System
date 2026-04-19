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
        Schema::create('document_vehicules', function (Blueprint $table) {
            $table->id();
            $table->morphs('documentable'); // génère documentable_id et documentable_type
            $table->string('type_doc'); // assurance, contrôle technique...
            $table->date('date_expiration');
            $table->string('fichier_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_vehicules');
    }
};
