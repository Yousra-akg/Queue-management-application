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
            $table->foreignId('entretien_id')->constrained()->onDelete('cascade');
            $table->string('codeUnique'); 
            $table->integer('numeroOrdre');
            $table->enum('statut', ['en attente', 'en cours', 'terminée'])->default('en attente');
            $table->foreignId('formateur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('salle_id')->nullable()->constrained('salles')->onDelete('set null');
            $table->dateTime('heureArrivee')->nullable();
            $table->dateTime('heureAppel')->nullable();
            $table->dateTime('heureFin')->nullable();
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
