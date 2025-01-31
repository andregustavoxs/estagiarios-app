<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('internship_agencies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->unique();
            $table->string('trade_name')->unique();
            $table->string('cnpj')->unique();
            $table->string('postal_code')->nullable();
            $table->string('address')->nullable();
            $table->string('number')->nullable();
            $table->string('complement')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('city')->nullable();
            $table->string('uf', 2)->nullable();
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
        Schema::dropIfExists('internship_agencies');
    }
};
