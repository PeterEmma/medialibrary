<?php

namespace CipeMotion\Medialibrary\Entities;

use Illuminate\Database\Eloquent\Model;

class Transformation extends Model
{
    protected $table = 'medialibrary_transformations';

    protected $fillable = [

    ];

    protected $visible = [

    ];

    // Scopes
    // ...

    // Getters
    public function __toString()
    {
        return $this->type;
    }

    // Setters
    // ...

    // Relations
    public function file()
    {
        return $this->belongsTo(File::class);
    }

    // Helpers
    // ...
}
