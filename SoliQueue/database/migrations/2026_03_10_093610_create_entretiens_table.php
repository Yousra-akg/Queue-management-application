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
        Schema::create('entretiens', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('nom');
        $table->date('dateEntretien');
        $table->time('heureDebut');
        $table->time('heureFin');
        $table->integer('capaciteMax');
        $table->string('codePresence');
        $table->enum('statut', ['planifiée', 'en cours', 'terminée', 'annulée'])->default('planifiée');        
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entretiens');
    }
};
