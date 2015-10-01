<?php

namespace CipeMotion\Medialibrary\Entities;

use Image;
use Storage;
use Rhumsaa\Uuid\Uuid;
use CipeMotion\Medialibrary\FileTypes;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class File extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'medialibrary_files';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'url',
        'name',
        'type',
        'size',
        'width',
        'height',
        'preview'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'url',
        'preview'
    ];

    /**
     * The URL generator instance for this file.
     *
     * @var \CipeMotion\Medialibrary\Generators\IUrlGenerator
     */
    protected $generator = null;

    /**
     * The local path to the file, used for transformations.
     *
     * @var string|null
     */
    protected $localPath = null;

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $relations = config('medialibrary.relations.attachment');

        if (in_array($method, $relations)) {
            return $this->morphedByMany($relations[$method], 'attachable');
        }

        if (starts_with($method, 'getUrl') && ends_with($method, 'Attribute') && $method !== 'getUrlAttribute') {
            return $this->getUrlAttribute(array_get($parameters, '0'));
        }

        return parent::__call($method, $parameters);
    }

    /**
     * Convert the model to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Get the local path.
     *
     * @return string
     */
    public function getLocalPath()
    {
        if (empty($this->localPath)) {
            $temp = get_temp_path();

            file_put_contents(
                $temp,
                Storage::disk($this->disk)->get("{$this->id}/upload.{$this->extension}")
            );

            $this->localPath = $temp;
        }

        return $this->localPath;
    }

    /**
     * Set the local path.
     *
     * @param string $path
     */
    public function setLocalPath($path)
    {
        $this->localPath = $path;
    }

    /**
     * Get the url.
     *
     * @param string|null $transformation
     *
     * @return string
     */
    public function getUrl($transformation = null)
    {
        if (!empty($transformation)) {
            /** @var \CipeMotion\Medialibrary\Entities\Transformation|null $transformation */
            $transformation = $this->transformations->where('name', $transformation)->first();

            if (!is_null($transformation) && $transformation->completed == false) {
                $transformation = null;
            }
        }

        return $this->getUrlGenerator()->getUrlForTransformation($this, $transformation);
    }

    /**
     * Get the url attribute.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return $this->getUrl();
    }

    /**
     * Get the name attribute.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        if (!is_null($this->attributes['name'])) {
            return $this->attributes['name'];
        }

        return $this->filename;
    }

    /**
     * Set the name attribute.
     *
     * @param string $value
     */
    public function setNameAttribute($value)
    {
        if (empty($value)) {
            $value = null;
        }

        $this->attributes['name'] = $value;
    }

    /**
     * Get the human readable file size.
     *
     * @return string
     */
    public function getSizeAttribute()
    {
        return filesize_to_human($this->attributes['size']);
    }

    /**
     * Get the url to a file preview.
     *
     * @return string|null
     */
    public function getPreviewAttribute()
    {
        return ($this->type === FileTypes::TYPE_IMAGE) ? $this->getUrl('thumb') : null;
    }

    /**
     * The file owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(config('medialibary.relations.owner'));
    }

    /**
     * The file category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * The transformations belonging to this file.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transformations()
    {
        return $this->hasMany(Transformation::class)->with(['file']);
    }

    /**
     * The models the file is attached to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function attachables()
    {
        return $this->morphTo();
    }

    /**
     * Get the url generator for this file.
     *
     * @return \CipeMotion\Medialibrary\Generators\IUrlGenerator
     */
    protected function getUrlGenerator()
    {
        if (is_null($this->generator)) {
            $this->generator = app(config('medialibrary.generator.url'), [config("filesystems.disks.{$this->disk}")]);
        }

        return $this->generator;
    }

    /**
     * File upload helper.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $upload
     * @param array                                               $attributes
     * @param string|null                                         $disk
     *
     * @return bool|\CipeMotion\Medialibrary\Entities\File
     */
    public static function uploadFile(UploadedFile $upload, array $attributes = [], $disk = null)
    {
        $file = new File;
        $disk = (is_null($disk)) ? config('medialibrary.disk') : $disk;

        $file->id       = Uuid::uuid4()->toString();
        $file->owner_id = auth()->user()->id;

        if (array_get($attributes, 'category', 0) > 0) {
            $file->category_id = array_get($attributes, 'category');
        }

        if (!empty(array_get($attributes, 'name'))) {
            $file->name = array_get($attributes, 'name');
        }

        if (!empty(array_has($attributes, 'caption'))) {
            $file->caption = array_get($attributes, 'caption');
        }

        $type = self::getTypeForMime($upload->getMimeType());

        if ($type === FileTypes::TYPE_IMAGE) {
            $image = Image::make($upload);

            $file->width  = $image->getWidth();
            $file->height = $image->getHeight();
        } else {
            $file->width  = null;
            $file->height = null;
        }

        $file->type      = $type;
        $file->disk      = $disk;
        $file->filename  = $upload->getClientOriginalName();
        $file->extension = strtolower($upload->getClientOriginalExtension());
        $file->mime_type = $upload->getMimeType();
        $file->size      = $upload->getSize();

        $file->hidden    = false;
        $file->completed = true;

        $success = \Storage::disk($disk)->put(
            "{$file->id}/upload.{$file->extension}",
            fopen($upload->getRealPath(), 'r')
        );

        if ($success) {
            $file->save();

            $file->setLocalPath($upload->getRealPath());

            return $file;
        }

        return $success;
    }

    /**
     * Find the type for the mime type.
     *
     * @param string $mime
     *
     * @return string|null
     */
    public static function getTypeForMime($mime)
    {
        $types = config('medialibrary.file_types');

        foreach ($types as $type => $data) {
            $allowed = array_flatten(array_values(array_get($data, 'mimes')));

            if (in_array($mime, $allowed)) {
                return $type;
            }
        }

        return null;
    }
}
