<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intern_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('evaluation_number');
            $table->boolean('is_completed')->default(false);
            $table->string('pdf_path')->nullable();
            $table->timestamps();

            // Ensure each internship can only have one of each evaluation number
            $table->unique(['internship_id', 'evaluation_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intern_evaluations');
    }
};
