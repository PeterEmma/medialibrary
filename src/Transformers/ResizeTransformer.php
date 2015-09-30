<?php

namespace CipeMotion\Medialibrary\Transformers;

use File as Filesystem;
use Image;
use Storage;
use CipeMotion\Medialibrary\Entities\File;
use CipeMotion\Medialibrary\Entities\Transformation;

class ResizeTransformer
{
    /**
     * The transformation name.
     *
     * @var string
     */
    protected $name;

    /**
     * The configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * Initialize the transformer.
     *
     * @param string $name
     * @param array  $config
     */
    public function __construct($name, array $config)
    {
        $this->name   = $name;
        $this->config = $config;
    }

    /**
     * Transform the source file.
     *
     * @param \CipeMotion\Medialibrary\Entities\File $file
     *
     * @return \CipeMotion\Medialibrary\Entities\Transformation
     */
    public function transform(File $file)
    {
        $destination    = get_temp_path();
        $transformation = new Transformation;

        $image = Image::make($file->getLocalPath())->resize(
            array_get($this->config, 'size.w'),
            array_get($this->config, 'size.h')
        )->save($destination);

        $transformation->name      = $this->name;
        $transformation->type      = $file->type;
        $transformation->size      = Filesystem::size($destination);
        $transformation->width     = array_get($this->config, 'size.w');
        $transformation->height    = array_get($this->config, 'size.h');
        $transformation->mime_type = $file->mime_type;
        $transformation->extension = $file->extension;
        $transformation->completed = true;

        Storage::disk($file->disk)->put(
            "{$file->id}/{$transformation->name}.{$transformation->extension}",
            fopen($destination, 'r')
        );

        return $transformation;
    }
}
