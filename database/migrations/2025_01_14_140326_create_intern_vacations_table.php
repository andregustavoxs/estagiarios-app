<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // First drop the existing table if it exists
        Schema::dropIfExists('intern_vacations');

        Schema::create('intern_vacations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedTinyInteger('period')->default(1);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days_taken');
            $table->text('observation')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('intern_vacations');
    }
};
