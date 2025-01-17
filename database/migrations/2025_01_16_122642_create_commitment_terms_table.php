<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('commitment_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained()->cascadeOnDelete();
            $table->boolean('intern_signature')->default(false);
            $table->timestamp('intern_signature_date')->nullable();
            $table->boolean('court_signature')->default(false);
            $table->timestamp('court_signature_date')->nullable();
            $table->boolean('institution_signature')->default(false);
            $table->timestamp('institution_signature_date')->nullable();
            $table->string('document_path')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('commitment_terms');
    }
};
