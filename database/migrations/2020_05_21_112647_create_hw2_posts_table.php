<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHw2PostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hw2_posts', function (Blueprint $table) {
            $table->id();
            $table->string("creator");
            $table->string("title");
            $table->string("url_img")->nullable();
            $table->datetime("date_and_time");
            $table->timestamps();

            $table->foreign("creator")->references("username")->on("users");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('hw2_posts');
    }
}
