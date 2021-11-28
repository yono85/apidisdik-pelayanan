<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->integer('type');
            $table->integer('groups');
            $table->bigInteger('from_id');
            $table->bigInteger('to_id');
            $table->string('title');
            $table->text('text');
            $table->text('url');
            $table->integer('read');
            $table->bigInteger('read_id');
            $table->string('read_date');
            $table->timestamps();
            $table->integeR('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifiactions');
    }
}
