<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAstreintesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('astreintes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date("date_start")->nullable();
            $table->date("date_end")->nullable();
            $table->time("heure_start")->nullable();
            $table->time("heure_end")->nullable();
            $table->string("color")->nullable();
            $table->integer("nbr_hours")->nullable();
            $table->string("description")->nullable();
            $table->timestamps();
        });

        Schema::create('astreinte_user', function (Blueprint $table) {
            $table->bigIncrements('id');                        
            $table->bigInteger("user_id")->unsigned();
            $table->bigInteger("astreinte_id")->unsigned();
        });

		Schema::table('astreinte_user', function(Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')
						->onDelete('restrict')
						->onUpdate('restrict');
			$table->foreign('astreinte_id')->references('id')->on('astreintes')
						->onDelete('restrict')
						->onUpdate('restrict');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('astreinte_user');
        Schema::dropIfExists('astreintes');
    }
}
