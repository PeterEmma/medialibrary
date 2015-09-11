<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMedialibraryCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medialibrary_categories', function (Blueprint $table) {

            // Primary key
            $table->increments('id');

            // Foreign key
            $table->integer('tenant_id')->unsigned();
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->onUpdate('CASCADE')
                  ->onDelete('CASCADE');

            $table->integer('parent_id')->unsigned()->nullable();
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('medialibrary_categories')
                  ->onUpdate('CASCADE')
                  ->onDelete('CASCADE');

            // Data
            $table->string('name');

            // Metadata
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medialibrary_categories');
    }
}
