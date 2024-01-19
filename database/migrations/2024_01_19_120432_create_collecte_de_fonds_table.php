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
        Schema::create('collecte_de_fonds', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->string('description');
            $table->string('image');
            $table->string('objectifFinancier');
            $table->string('numeroCompte');
            $table->enum('statut', ['encours', 'cloturer', 'decloturer'])->default('encours');
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collecte_de_fonds');
    }
};
