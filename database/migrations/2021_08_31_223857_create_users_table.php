<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->string('token');
            $table->string('name');
            $table->integer('gender');
            $table->string('birth');
            $table->string('email');
            $table->string('password');
            $table->integer('level'); //0 people, 1 admin diknas, 2 admin sekolah, 9 administrator
            $table->integer('sublevel'); //
            $table->integer('company_id');
            $table->integer('register_status');
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
        Schema::dropIfExists('users');
    }
}
