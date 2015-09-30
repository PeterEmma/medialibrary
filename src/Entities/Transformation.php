<?php

namespace CipeMotion\Medialibrary\Entities;

use Illuminate\Database\Eloquent\Model;

class Transformation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'medialibrary_transformations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

    ];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = [

    ];

    /**
     * Convert the model to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->type;
    }

    /**
     * The file this is a transformation for.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
