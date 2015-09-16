<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMedialibraryFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medialibrary_files', function (Blueprint $table) {

            // Primary key
            $table->char('id', 36);
            $table->primary('id');

            // Foreign keys
            /** @var \Illuminate\Database\Eloquent\Model $owner */
            $owner = new config('medialibrary.relations.owner');

            $table->integer('owner_id')->unsigned();
            $table->foreign('owner_id')
                  ->references($owner->getKeyName())
                  ->on($owner->getTable())
                  ->onUpdate('CASCADE')
                  ->onDelete('CASCADE');

            if (!is_null(config('medialibrary.relations.user'))) {
                /** @var \Illuminate\Database\Eloquent\Model $user */
                $user = new config('medialibrary.relations.user');

                $table->integer('user_id')->unsigned()->nullable();
                $table->foreign('user_id')
                      ->references($user->getKeyName())
                      ->on($user->getTable())
                      ->onUpdate('CASCADE')
                      ->onDelete('SET NULL');
            }

            $table->integer('category_id')->unsigned()->nullable();
            $table->foreign('category_id')
                  ->references('id')
                  ->on('medialibrary_categories')
                  ->onUpdate('CASCADE')
                  ->onDelete('SET NULL');

            // Data
            $table->string('name')->nullable();
            $table->text('caption')->nullable();

            // Metadata
            $table->timestamps();
            $table->smallInteger('width');
            $table->smallInteger('height');
            $table->text('properties')->nullable();

            // Properties
            $table->string('disk');
            $table->string('filename');
            $table->string('extension');
            $table->string('mime_type');
            $table->integer('size');

            // Flags
            $table->boolean('hidden')->default(false);
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
        Schema::dropIfExists('medialibrary_files');
    }
}
