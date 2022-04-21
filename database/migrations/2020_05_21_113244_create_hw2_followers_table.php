<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHw2FollowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hw2_followers', function (Blueprint $table) {
            $table->string("user_username");
            $table->string("user_followed");
            $table->timestamps();

            $table->foreign("user_username")->references("username")->on("users");
            $table->foreign("user_followed")->references("username")->on("users");            
            $table->primary(['user_username', 'user_followed']);
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
        Schema::dropIfExists('hw2_followers');
    }
}
