<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('token');
            $table->integer('type'); //1. negeri, 0. swasta
            $table->integer('jenjang'); //jenjang
            $table->string('npsn');
            $table->string('name');
            $table->string('alias');
            $table->text('address');
            $table->string('kelurahan');
            $table->integer('kecamatan');
            $table->integer('city');
            $table->integer('provinsi');
            $table->integer('kodepos');
            $table->string('phone');
            $table->string('email');
            $table->text('detail');
            $table->integer('zone');
            $table->timestamps();
            $table->integer('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schools');
    }
}
