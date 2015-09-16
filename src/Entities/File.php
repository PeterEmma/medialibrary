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

        // Assign a UUID to each file
        self::creating(function (File $file) {
            $file->id = Uuid::uuid4();
        });
    }

    // Catch any attachment relations
    public function __call($method, $parameters)
    {
        $relations = config('medialibary.relations.attachment');

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
    public function owner()
    {
        return $this->belongsTo(config('medialibary.relations.owner'));
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function attachables()
    {
        return $this->morphTo();
    }

    // Helpers
    // ...
}
