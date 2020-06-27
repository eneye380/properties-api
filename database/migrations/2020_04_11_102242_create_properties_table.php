<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'properties', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->string('name');
                $table->string('type');
                $table->integer('homes');
                $table->integer('units');
                $table->integer('spaces');
                $table->text('description');
                $table->string('state');
                $table->string('lga');
                $table->string('town');
                $table->string('address');
                $table->string('latitude');
                $table->string('longitude');

            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('properties');
    }
}
