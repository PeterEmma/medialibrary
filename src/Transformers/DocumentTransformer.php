<?php

namespace CipeMotion\Medialibrary\Transformers;

use Image;
use Storage;
use CloudConvert\Api;
use File as Filesystem;
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
            'inputformat'      => $file->extension,
            'outputformat'     => $extension,
            'input'            => 'download',
            'wait'             => true,
            'file'             => $file->downloadUrl,
            'converteroptions' => [
                'page_range' => '1-1',
            ],
        ];

        if (!is_null(config('services.cloudconvert.timeout'))) {
            $cloudconvertSettings['timeout'] = config('services.cloudconvert.timeout');
        }

        // Run the conversion
        $convert = $this->api->convert($cloudconvertSettings)->wait();

        // Get a temp path
        $destination = get_temp_path();

        // Download the converted video file
        copy('https:' . $convert->output->url, $destination);

        // We got it all, cleanup!
        $convert->delete();

        // Get the disk and a stream from the cropped image location
        $disk   = Storage::disk($file->disk);
        $stream = fopen($destination, 'r+');

        // Upload the preview
        $disk->put("{$file->id}/preview.{$extension}", $stream);

        // Cleanup our streams
        if (is_resource($stream)) {
            fclose($stream);
        }

        // Create a Image
        /** @var \Intervention\Image\Image $image */
        $image = Image::make($destination);

        // Build the transformation
        $preview            = new Transformation;
        $preview->name      = 'preview';
        $preview->size      = Filesystem::size($destination);
        $preview->mime_type = $image->mime();
        $preview->type      = File::getTypeForMime($preview->mime_type);
        $preview->width     = $image->width();
        $preview->height    = $image->height();
        $preview->extension = $extension;
        $preview->completed = true;

        // Store the preview
        $file->transformations()->save($preview);

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

        // Stora a cropped version
        $image->save($destination);

        // Build the transformation
        $transformation            = new Transformation;
        $transformation->name      = 'thumb';
        $transformation->type      = $preview->type;
        $transformation->size      = Filesystem::size($destination);
        $transformation->width     = $image->width();
        $transformation->height    = $image->height();
        $transformation->mime_type = $preview->mime_type;
        $transformation->extension = $preview->extension;
        $transformation->completed = true;

        // Cleanup the image
        $image->destroy();

        // Get the disk and a stream from the cropped image location
        $stream = fopen($destination, 'r+');

        // Upload the preview
        $disk->put("{$file->id}/{$transformation->name}.{$transformation->extension}", $stream);

        // Cleanup our streams
        if (is_resource($stream)) {
            fclose($stream);
        }

        return $transformation;
    }
}
