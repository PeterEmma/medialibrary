<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenamedHiddenToIsHiddenMedialibraryFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medialibrary_files', function (Blueprint $table) {
            $table->renameColumn('hidden', 'is_hidden');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('medialibrary_files', function (Blueprint $table) {
            $table->renameColumn('is_hidden', 'hidden');
        });
    }
}
