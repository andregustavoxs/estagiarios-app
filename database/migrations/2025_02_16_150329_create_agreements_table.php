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
        Schema::create('agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_agency_id')->constrained()->cascadeOnDelete();
            $table->string('agreement_number')->unique(); // nº do convênio
            $table->date('agreement_validity_start'); // período de validade do convênio (start date)
            $table->date('agreement_validity_end'); // período de validade do convênio (end date)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agreements');
    }
};
