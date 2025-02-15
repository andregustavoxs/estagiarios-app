<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('internships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intern_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('department_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('supervisor_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('course_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('educational_institution_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->string('registration_number')->unique();
            $table->enum('education_level', ['postgraduate', 'higher_education', 'technical'])->required();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('internships');
    }
};
