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
        Schema::table('interns', function (Blueprint $table) {
            // Drop the existing foreign key and column
            $table->dropForeign(['internship_agency_id']);
            $table->dropColumn('internship_agency_id');

            // Recreate the column as non-nullable with restrict on delete
            $table->foreignId('internship_agency_id')
                ->constrained('internship_agencies')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interns', function (Blueprint $table) {
            // Drop the existing foreign key and column
            $table->dropForeign(['internship_agency_id']);
            $table->dropColumn('internship_agency_id');

            // Recreate the column as nullable with set null on delete
            $table->foreignId('internship_agency_id')
                ->after('course_id')
                ->nullable()
                ->constrained('internship_agencies')
                ->onDelete('set null');
        });
    }
};
