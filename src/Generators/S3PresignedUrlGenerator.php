<?php

namespace CipeMotion\Medialibrary\Generators;

use Aws\S3\S3Client;
use CipeMotion\Medialibrary\FileTypes;
use CipeMotion\Medialibrary\Entities\File;
use CipeMotion\Medialibrary\Entities\Transformation;

class S3PresignedUrlGenerator implements IUrlGenerator
{
    /**
     * The config.
     *
     * @var array
     */
    protected $config;

    /**
     * The S3 client.
     *
     * @var \Aws\S3\S3Client
     */
    protected $client;

    /**
     * Instantiate the URL generator.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        $this->client = new S3Client([
            'region'      => array_get($this->config, 'region'),
            'version'     => '2006-03-01',
            'credentials' => [
                'key'    => array_get($this->config, 'key'),
                'secret' => array_get($this->config, 'secret'),
            ],
        ]);
    }

    /**
     * Get a URL to the resource.
     *
     * @param \CipeMotion\Medialibrary\Entities\File                $file
     * @param \CipeMotion\Medialibrary\Entities\Transformation|null $transformation
     * @param bool                                                  $fullPreview
     * @param bool                                                  $download
     *
     * @return string
     */
    public function getUrlForTransformation(
        File $file,
        Transformation $transformation = null,
        $fullPreview = false,
        $download = false
    ) {
        if (empty($transformation)) {
            $tranformationName = 'upload';
            $extension         = $file->extension;
            $mime              = $file->mime;

            if ($fullPreview && $file->type !== FileTypes::TYPE_IMAGE) {
                $tranformationName = 'preview';
                $extension         = 'jpg';
            }
        } else {
            $tranformationName = $transformation->name;
            $extension         = $transformation->extension;
            $mime              = $transformation->mime;
        }

        $commandParams = [
            'ResponseCacheControl' => 'private, max-age=1200',
            'ResponseContentType'  => $mime,
            'Bucket'               => array_get($this->config, 'bucket'),
            'Key'                  => "{$file->id}/{$tranformationName}.{$extension}",
        ];

        if ($download) {
            $commandParams['ResponseContentDisposition'] = "attachment; filename={$file->filename}-{$tranformationName}.{$extension}";
        }

        $expires = array_get($this->config, 'presigned.expires', '+20 minutes');
        $command = $this->client->getCommand('GetObject', $commandParams);

        return (string)$this->client->createPresignedRequest($command, $expires)->getUri();
    }
}
