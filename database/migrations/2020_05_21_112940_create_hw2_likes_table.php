<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHw2LikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hw2_likes', function (Blueprint $table) {
            $table->string("username");
            $table->unsignedBigInteger("post");
            $table->timestamps();

            $table->foreign("username")->references("username")->on("users");
            $table->foreign("post")->references("id")->on("hw2_posts");
            $table->primary(['username', 'post']);
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
        Schema::dropIfExists('hw2_likes');
    }
}
