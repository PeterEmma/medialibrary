<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMedialibraryAttachableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medialibrary_attachable', function (Blueprint $table) {

            // Foreign key
            $table->char('file_id', 36);
            $table->foreign('file_id')
                  ->references('id')
                  ->on('medialibrary_files')
                  ->onUpdate('CASCADE')
                  ->onDelete('CASCADE');

            $table->string('attachable_type');
            $table->integer('attachable_id')->unsigned();

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
        Schema::dropIfExists('medialibrary_attachable');
    }
}
