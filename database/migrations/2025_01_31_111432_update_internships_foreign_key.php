<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('internships', function (Blueprint $table) {
            // Drop the existing foreign key
            $table->dropForeign(['intern_id']);
            
            // Add the new foreign key with cascade delete
            $table->foreign('intern_id')
                ->references('id')
                ->on('interns')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('internships', function (Blueprint $table) {
            // Drop the cascade delete foreign key
            $table->dropForeign(['intern_id']);
            
            // Restore the original foreign key with restrict
            $table->foreign('intern_id')
                ->references('id')
                ->on('interns')
                ->onDelete('restrict');
        });
    }
};
