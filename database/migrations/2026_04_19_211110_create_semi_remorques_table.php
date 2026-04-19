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
        Schema::create('semi_remorques', function (Blueprint $table) {
            $table->id();
            $table->string('matricule')->unique();
            $table->string('marque');
            $table->string('type_remorque');
            $table->decimal('ptac', 8, 2);
            $table->string('vin')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semi_remorques');
    }
};
