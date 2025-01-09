<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('intern_vacations', function (Blueprint $table) {
            $table->integer('days_taken')->after('end_date')->nullable();
        });

        // Update existing records
        DB::statement('
            UPDATE intern_vacations 
            SET days_taken = DATEDIFF(end_date, start_date) + 1 
            WHERE start_date IS NOT NULL AND end_date IS NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('intern_vacations', function (Blueprint $table) {
            $table->dropColumn('days_taken');
        });
    }
};
