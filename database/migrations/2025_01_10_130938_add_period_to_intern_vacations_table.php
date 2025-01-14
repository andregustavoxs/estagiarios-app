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
        Schema::table('intern_vacations', function (Blueprint $table) {
            $table->unsignedTinyInteger('period')->default(1)->after('intern_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('intern_vacations', function (Blueprint $table) {
            $table->dropColumn('period');
        });
    }
};
