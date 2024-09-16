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
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->date('date_presence');
            $table->enum('status', ['present', 'absent'])->default('present');
            $table->string('motif');
            $table->string('justification');
            $table->foreignId('classe_eleve_id')->constrained('classe_eleves')->onDelete('cascade');
            $table->foreignId('classe_prof_id')->constrained('classe_profs')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presences');
    }
};
