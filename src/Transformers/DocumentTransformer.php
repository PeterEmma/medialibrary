<?php

namespace CipeMotion\Medialibrary\Transformers;

use Image;
use Storage;
use CloudConvert\Api;
use File as Filesystem;
use CipeMotion\Medialibrary\FileTypes;
use CipeMotion\Medialibrary\Entities\File;
use CipeMotion\Medialibrary\Entities\Transformation;

class DocumentTransformer implements ITransformer
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
     * The cloudconvert API.
     *
     * @var array
     */
    protected $api;

    /**
     * Initialize the transformer.
     *
     * @param string $name
     * @param array  $config
     */
    public function __construct($name, array $config)
    {
        $this->api    = new Api(config('services.cloudconvert.key'));

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
        $extension = array_get($this->config, 'extension', 'jpg');

        $cloudconvertSettings = [
            'inputformat'  => $file->extension,
            'outputformat' => $extension,
            'input'        => 'upload',
            'wait'         => true,
            'file'         => fopen($file->url, 'r'),
            'converteroptions' => [
                'page_range' => '1-1'
            ]
        ];

        if (!is_null(config('services.cloudconvert.timeout'))) {
            $cloudconvertSettings['timeout'] = config('services.cloudconvert.timeout');
        }

        $convert = $this->api->convert($cloudconvertSettings)->wait();

        $contents = file_get_contents('https:' . $convert->output->url . '?inline');

        Storage::disk($file->disk)->put("{$file->id}/preview.{$extension}", $contents);

        // Create a Image
        $image = Image::make($contents);

        // Save the the transformation
        $preview            = new Transformation;
        $preview->name      = 'preview';
        $preview->size      = $image->filesize();
        $preview->mime_type = $image->mime();
        $preview->type      = File::getTypeForMime($preview->mime_type);
        $preview->width     = $image->width();
        $preview->height    = $image->height();
        $preview->extension = $extension;
        $preview->completed = true;

        $file->transformations()->save($preview);

        // Create a thumb
        $destination = get_temp_path();

        if (array_get($this->config, 'fit', false)) {
            $image->fit(
                array_get($this->config, 'size.w', null),
                array_get($this->config, 'size.h', null),
                function ($constraint) {
                    if (!array_get($this->config, 'upsize', true)) {
                        $constraint->upsize();
                    }
                },
                'top'
            );
        } else {
            $image->resize(
                array_get($this->config, 'size.w', null),
                array_get($this->config, 'size.h', null),
                function ($constraint) {
                    if (array_get($this->config, 'aspect', true)) {
                        $constraint->aspectRatio();
                    }

                    if (!array_get($this->config, 'upsize', true)) {
                        $constraint->upsize();
                    }
                }
            );
        }
        $image->save($destination);

        $transformation = new Transformation;

        $transformation->name      = 'thumb';
        $transformation->type      = $preview->type;
        $transformation->size      = Filesystem::size($destination);
        $transformation->width     = $image->width();
        $transformation->height    = $image->height();
        $transformation->mime_type = $preview->mime_type;
        $transformation->extension = $preview->extension;
        $transformation->completed = true;

        Storage::disk($file->disk)->put(
            "{$file->id}/{$transformation->name}.{$transformation->extension}",
            file_get_contents($destination)
        );

        return $transformation;
    }
}
