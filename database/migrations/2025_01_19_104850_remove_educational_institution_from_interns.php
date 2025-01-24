<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('interns', function (Blueprint $table) {
            $table->dropForeign(['educational_institution_id']);
            $table->dropColumn('educational_institution_id');
        });
    }

    public function down()
    {
        Schema::table('interns', function (Blueprint $table) {
            $table->foreignId('educational_institution_id')
                ->constrained()
                ->restrictOnDelete();
        });
    }
};
