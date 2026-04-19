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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom complet (utilisé par Breeze)
            $table->string('username')->unique(); // Demandé dans tes specs
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // --- AJOUTS POUR TON APPLICATION ---
            $table->string('firstname')->nullable(); // Prénom
            $table->string('lastname')->nullable();  // Nom
            $table->string('phone')->nullable();
            $table->enum('civility', ['Homme', 'Femme'])->nullable();
            $table->string('organisation')->nullable(); // Pour le détail utilisateur
            $table->boolean('is_active')->default(true); // Statut actif/inactif
            $table->timestamp('last_login_at')->nullable(); // Pour la traçabilité
            // ------------------------------------
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
