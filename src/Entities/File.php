<?php

namespace CipeMotion\Medialibrary\Entities;

use Rhumsaa\Uuid\Uuid;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'medialibrary_files';

    public $incrementing = false;

    protected $fillable = [

    ];

    protected $visible = [

    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function (File $file) {
            $file->id = Uuid::uuid4();
        });
    }

    public function __call($method, $parameters)
    {
        $relations = config('medialibrary.relations');

        if (in_array($method, $relations)) {
            return $this->morphedByMany($relations[$method], 'attachable');
        }

        return parent::__call($method, $parameters);
    }

    // Scopes
    // ...

    // Getters
    public function __toString()
    {
        return $this->name;
    }
    public function getNameAttribute()
    {
        if (!is_null($this->attributes['name'])) {
            return $this->attributes['name'];
        }

        return $this->filename;
    }

    // Setters
    public function setNameAttribute($value)
    {
        if (empty($value)) {
            $value = null;
        }

        $this->attributes['name'] = $value;
    }

    // Relations
    public function attachables()
    {
        return $this->morphTo();
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Helpers
    // ...
}
