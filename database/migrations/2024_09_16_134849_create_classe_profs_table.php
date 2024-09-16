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
        Schema::create('classe_profs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('annee_classe_id')->constrained('annee_classes')->onDelete('cascade');
            $table->foreignId('prof_mat_id')->constrained('prof_matieres')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classe_profs');
    }
};
