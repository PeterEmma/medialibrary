<?php

namespace CipeMotion\Medialibrary\Entities;

use Illuminate\Database\Eloquent\Builder;
use Image;
use Storage;
use Rhumsaa\Uuid\Uuid;
use CipeMotion\Medialibrary\FileTypes;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
}
