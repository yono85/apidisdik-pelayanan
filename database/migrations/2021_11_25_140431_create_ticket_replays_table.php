<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketReplaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_replays', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->integer('type');
            $table->string('token');
            $table->bigInteger('ticket_id');
            $table->text('text');
            $table->text('url_file');
            $table->bigInteger('user_id');
            $table->timestamps();
            $table->string('date');
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
        Schema::dropIfExists('ticket_replays');
    }
}
