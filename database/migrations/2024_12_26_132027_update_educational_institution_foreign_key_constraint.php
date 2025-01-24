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
            $table->dropForeign(['educational_institution_id']);
            $table->dropColumn('educational_institution_id');

            // Recreate the column as non-nullable with restrict on delete
            $table->foreignId('educational_institution_id')
                ->constrained('educational_institutions')
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
            $table->dropForeign(['educational_institution_id']);
            $table->dropColumn('educational_institution_id');

            // Recreate the column as nullable with set null on delete
            $table->foreignId('educational_institution_id')
                ->after('course_id')
                ->nullable()
                ->constrained('educational_institutions')
                ->onDelete('set null');
        });
    }
};
