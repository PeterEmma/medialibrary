<?php

namespace CipeMotion\Medialibrary\Entities;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'medialibrary_categories';

    protected $fillable = [
        'name'
    ];

    protected $visible = [
        'name'
    ];

    // Scopes
    // ...

    // Getters
    // ...

    // Setters
    // ...

    // Relations
    public function parent()
    {
        return $this->belongsTo(self::class);
    }

    // Helpers
    // ...
}
