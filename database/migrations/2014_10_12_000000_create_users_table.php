<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->string('nom');
            $table->string('prenom')->nullable();
            $table->string('image')->nullable(); 
            $table->string('description')->nullable();
            $table->string('adresse')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('telephone');
            $table->enum('role',['admin','donateur','fondation'])->default('donateur');
            $table->rememberToken();
            $table->enum('statut', ['enattente', 'accepte', 'refuse']);
            $table->boolean('bloque')->default(false);
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
        //DB::table('users')->update(['statut' => DB::raw("CASE WHEN role = 'fondation' THEN 'enattente' ELSE 'accepte' END")]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
