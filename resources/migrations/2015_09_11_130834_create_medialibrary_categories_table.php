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
            /** @var \Illuminate\Database\Eloquent\Model $owner */
            $owner = app(config('medialibrary.relations.owner.model'));

            $table->integer('owner_id')->unsigned();
            $table->foreign('owner_id')
                  ->references($owner->getKeyName())
                  ->on($owner->getTable())
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
            $table->integer('order')->unsigned()->default(0);

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
