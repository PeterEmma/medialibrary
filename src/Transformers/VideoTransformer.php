<?php

namespace CipeMotion\Medialibrary\Transformers;

use Image;
use Storage;
use CloudConvert\Api;
use File as Filesystem;
use CipeMotion\Medialibrary\Entities\File;
use CipeMotion\Medialibrary\Entities\Transformation;

class VideoTransformer implements ITransformer
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
        // Extract config options
        $extension       = array_get($this->config, 'extension', 'mp4');
        $videoCodec      = strtolower(array_get($this->config, 'video.codec', 'h264'));
        $videoResolution = array_get($this->config, 'video.resolution', '1280x720');
        $audioCodec      = strtolower(array_get($this->config, 'audio.codec', 'aac'));

        // Retrieve the file info and generate a thumb
        list($preview, $thumb, $fileInfo) = $this->generateThumbAndRetrieveFileInfo($file);

        // Build up the base settings
        $cloudconvertSettings = [
            'inputformat'      => $file->extension,
            'outputformat'     => $extension,
            'wait'             => true,
            'input'            => 'download',
            'file'             => $file->downloadUrl,
            'converteroptions' => [
                'video_codec' => 'copy',
                'audio_codec' => 'copy',
                'faststart'   => true,
            ],
        ];

        // Collect the streams from the video info
        $streams = collect($fileInfo->info->streams);

        // Find the video stream with the correct codec if any
        $videoStream = $streams->first(function ($stream) use ($videoCodec, $videoResolution) {
            return ($stream->codec_type === 'video' &&
                    strtolower($stream->codec_name) === $videoCodec &&
                    "{$stream->width}x{$stream->height}" === $videoResolution);
        });

        // If there is no compataible video stream, reencode it
        if (empty($videoStream)) {
            $cloudconvertSettings['converteroptions']['video_codec']      = $videoCodec;
            $cloudconvertSettings['converteroptions']['video_resolution'] = $videoResolution;
        }

        // Find the audio stream with the correct codec if any
        $audioStream = $streams->first(function ($stream) use ($audioCodec) {
            return ($stream->codec_type === 'audio' && strtolower($stream->codec_name) === $audioCodec);
        });

        // If there is no compataible audio stream, reencode it
        if (empty($audioStream)) {
            $cloudconvertSettings['converteroptions']['audio_codec'] = $audioCodec;
        }

        // Respect the cloudconvert timeout
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
        $fileInfo->delete();
        $convert->delete();

        // Setup the transformation properties
        $transformation            = new Transformation;
        $transformation->name      = $this->name;
        $transformation->type      = $file->type;
        $transformation->size      = Filesystem::size($destination);
        $transformation->width     = explode('x', $videoResolution)[0];
        $transformation->height    = explode('x', $videoResolution)[1];
        $transformation->mime_type = Filesystem::mimeType($destination);
        $transformation->extension = $extension;
        $transformation->completed = true;

        // Get the disk and a stream from the cropped image location
        $disk   = Storage::disk($file->disk);
        $stream = fopen($destination, 'rb');

        // Either overwrite the original uploaded file or write to the transformation path
        if (array_get($this->config, 'default', false)) {
            $disk->put("{$file->id}/upload.{$transformation->extension}", $stream);

            if ($transformation->extension !== $file->extension) {
                $disk->delete("{$file->id}/upload.{$file->extension}");
            }
        } else {
            $disk->put("{$file->id}/{$transformation->name}.{$transformation->extension}", $stream);
        }

        // Save the preview and thumb transformations
        $file->transformations()->save($thumb);
        $file->transformations()->save($preview);

        // Close the stream again
        if (is_resource($stream)) {
            fclose($stream);
        }

        return $transformation;
    }

    /**
     * Generate a thumb and retrieve info about the video file.
     *
     * @param \CipeMotion\Medialibrary\Entities\File $file
     *
     * @return array|\CipeMotion\Medialibrary\Entities\Transformation|\CloudConvert\Process
     */
    private function generateThumbAndRetrieveFileInfo(File $file)
    {
        // Extract config options
        $previewWidth = array_get($this->config, 'preview.size.w', 1280);

        // Build up the base settings
        $cloudconvertSettings = [
            'mode'             => 'info',
            'wait'             => true,
            'input'            => 'download',
            'file'             => $file->downloadUrl,
            'converteroptions' => [
                'thumbnail_format' => 'jpg',
                'thumbnail_size'   => "{$previewWidth}x",
            ],
        ];

        // Respect the cloudconvert timeout
        if (!empty(config('services.cloudconvert.timeout'))) {
            $cloudconvertSettings['timeout'] = config('services.cloudconvert.timeout');
        }

        // Execute our info request and wait for the output
        $info = $this->api->convert($cloudconvertSettings)->wait();

        // Generate a temp path
        $destination = get_temp_path();

        // Copy the thumb to our temp path
        copy('https:' . $info->output->url, $destination);

        // Get the disk and a stream from the cropped image location
        $disk   = Storage::disk($file->disk);
        $stream = fopen($destination, 'rb');

        // Store the preview image
        $disk->put("{$file->id}/preview.jpg", $stream);

        // Cleanup our streams
        if (is_resource($stream)) {
            fclose($stream);
        }

        // Create a image instance so we can detect the width and height
        /** @var \Intervention\Image\Image $image */
        $image = Image::make($destination);

        // Build the preview
        $preview            = new Transformation;
        $preview->name      = 'preview';
        $preview->size      = Filesystem::size($destination);
        $preview->mime_type = $image->mime();
        $preview->type      = File::getTypeForMime($preview->mime_type);
        $preview->width     = $image->width();
        $preview->height    = $image->height();
        $preview->extension = 'jpg';
        $preview->completed = true;

        // Resize the preview for a thumb
        if (array_get($this->config, 'thumb.fit', false)) {
            $image->fit(
                array_get($this->config, 'thumb.size.w', null),
                array_get($this->config, 'thumb.size.h', null),
                function ($constraint) {
                    if (!array_get($this->config, 'thumb.upsize', true)) {
                        $constraint->upsize();
                    }
                },
                'top'
            );
        } else {
            $image->resize(
                array_get($this->config, 'thumb.size.w', null),
                array_get($this->config, 'thumb.size.h', null),
                function ($constraint) {
                    if (array_get($this->config, 'thumb.aspect', true)) {
                        $constraint->aspectRatio();
                    }

                    if (!array_get($this->config, 'thumb.upsize', true)) {
                        $constraint->upsize();
                    }
                }
            );
        }

        // Store the thumb version
        $image->save($destination);

        // Build the thumb transformation
        $thumb            = new Transformation;
        $thumb->name      = 'thumb';
        $thumb->size      = Filesystem::size($destination);
        $thumb->mime_type = $image->mime();
        $thumb->type      = File::getTypeForMime($thumb->mime_type);
        $thumb->width     = $image->width();
        $thumb->height    = $image->height();
        $thumb->extension = 'jpg';
        $thumb->completed = true;

        // Cleanup the image
        $image->destroy();

        // Get the disk and a stream from the cropped image location
        $stream = fopen($destination, 'rb');

        // Upload the preview
        $disk->put("{$file->id}/{$thumb->name}.{$thumb->extension}", $stream);

        // Cleanup our streams
        if (is_resource($stream)) {
            fclose($stream);
        }

        return [$preview, $thumb, $info];
    }
}
