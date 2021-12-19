<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->integer('type_ticket');
            $table->string('kode');
            $table->string('token');
            $table->integer('level');
            $table->integer('type'); //
            $table->integer('subtype');
            $table->integer('pelayanan');
            $table->text('detail');
            $table->text('field');
            $table->text('url_file');
            $table->bigInteger('user_id');
            $table->integer('progress'); //0 = open, 1 = progress, 2 = done
            $table->timestamps();
            $table->string('date');
            $table->integer('status'); //1 active, 0 deleted
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
