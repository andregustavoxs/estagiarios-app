<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->foreignId('supervisor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('extension', 4)->unique()->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn(['supervisor_id', 'extension']);
        });
    }
};
