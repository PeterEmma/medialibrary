<?php

namespace CipeMotion\Medialibrary\Entities;

use Image;
use Storage;
use Illuminate\Database\Eloquent\Model;

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
