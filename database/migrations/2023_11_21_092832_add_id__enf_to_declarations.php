<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('declarations', function (Blueprint $table) {
            $table->unsignedBigInteger('id_Enf')->nullable();

            $table->foreign('id_Enf')
                ->references('id')
                ->on('enfants')
                ->onDelete('set null'); // Set the onDelete behavior to 'set null'
        });
    }

    public function down()
    {
        Schema::table('declarations', function (Blueprint $table) {
            $table->dropForeign(['id_Enf']);
            $table->dropColumn('id_Enf');
        });
    }
};
