<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('educational_institutions', function (Blueprint $table) {
            $table->string('postal_code')->nullable();
            $table->string('address')->nullable();
            $table->string('number')->nullable();
            $table->string('complement')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('city')->nullable();
            $table->string('uf', 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('educational_institutions', function (Blueprint $table) {
            $table->dropColumn([
                'postal_code',
                'address',
                'number',
                'complement',
                'neighborhood',
                'city',
                'uf',
            ]);
        });
    }
};
