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
        Schema::table('notifications', function (Blueprint $table) {
            $table->boolean('is_read')->default(false);  // Indique si la notification a été lue
            $table->timestamp('read_at')->nullable();    // Date à laquelle la notification a été lue
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('is_read');
            $table->dropColumn('read_at');
        });
    }
};
