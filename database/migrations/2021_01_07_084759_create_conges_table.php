<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCongesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conges', function (Blueprint $table) {
            $table->id();
            $table->date("date_start_conge")->nullable();
            $table->date("date_end_conge")->nullable();
            $table->timestamps();
        });

        Schema::create('conge_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->unsignedBigInteger("conge_id");
            $table->timestamps();
        });
        
		Schema::table('conge_user', function(Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')
						->onDelete('restrict')
                        ->onUpdate('restrict');
                        
            $table->foreign('conge_id')->references('id')->on('conges')
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
        Schema::dropIfExists('conge_user');
        Schema::dropIfExists('conges');
    }
}
