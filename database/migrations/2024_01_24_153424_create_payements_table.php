<?php

use App\Models\collecteDeFond;
use App\Models\collecteDeFonds;
use App\Models\User;
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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('token')->nullable();
            $table->integer('amount');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // $table->foreignId('collecte_de_fond_id')->constrained()->onDelete('cascade');
            $table->foreignIdFor(collecteDeFond::class)->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payements');
    }
};
