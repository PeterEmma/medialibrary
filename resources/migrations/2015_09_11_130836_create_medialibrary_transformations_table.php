<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMedialibraryTransformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medialibrary_transformations', function (Blueprint $table) {

            // Primary keys
            $table->increments('id');

            // Foreign keys
            $table->char('file_id', 36);
            $table->foreign('file_id')
                  ->references('id')
                  ->on('medialibrary_files')
                  ->onUpdate('CASCADE')
                  ->onDelete('CASCADE');

            // Metadata
            $table->timestamps();
            $table->smallInteger('width')->nullable();
            $table->smallInteger('height')->nullable();
            $table->text('properties')->nullable();

            // Properties
            $table->string('name');
            $table->string('type');
            $table->string('disk');
            $table->string('filename');
            $table->string('extension');
            $table->string('mime_type');
            $table->integer('size');

            // Flags
            $table->boolean('completed')->default(false);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medialibrary_transformations');
    }
}
