<?php

namespace CipeMotion\Medialibrary\Entities;

use Image;
use Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Attachable extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'medialibrary_attachable';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Scope the query to select attachables by file (id).
     *
     * @param \Illuminate\Database\Eloquent\Builder         $query
     * @param string|\CipeMotion\Medialibrary\Entities\File $file
     */
    public function scopeForFile(Builder $query, $file)
    {
        $fileId = ($file instanceof File) ? $file->id : $file;

        $query->where('file_id', $fileId);
    }
}
