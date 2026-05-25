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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidat_id')->constrained()->onDelete('cascade');
            $table->foreignId('session_id')->constrained()->onDelete('cascade');
            $table->string('codeUnique'); 
            $table->integer('numeroOrdre');
            $table->enum('statut', ['en attente', 'en cours', 'terminée'])->default('en attente');
            $table->dateTime('heureArrivee');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
