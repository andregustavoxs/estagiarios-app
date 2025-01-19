<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('interns', function (Blueprint $table) {
            $table->dropForeign(['internship_agency_id']);
            $table->dropColumn('internship_agency_id');
        });
    }

    public function down()
    {
        Schema::table('interns', function (Blueprint $table) {
            $table->foreignId('internship_agency_id')
                ->constrained()
                ->restrictOnDelete();
        });
    }
};
