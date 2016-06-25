<?php

namespace CipeMotion\Medialibrary\Generators;

use CipeMotion\Medialibrary\FileTypes;
use CipeMotion\Medialibrary\Entities\File;
use CipeMotion\Medialibrary\Entities\Transformation;

class S3UrlGenerator implements IUrlGenerator
{
    /**
     * The config.
     *
     * @var array
     */
    protected $config;

    /**
     * Instantiate the URL generator.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get a URL to the resource.
     *
     * @param \CipeMotion\Medialibrary\Entities\File                $file
     * @param \CipeMotion\Medialibrary\Entities\Transformation|null $tranformation
     * @param bool                                                  $fullPreview
     *
     * @return string
     */
    public function getUrlForTransformation(File $file, Transformation $tranformation = null, $fullPreview = false)
    {
        $region = array_get($this->config, 'region');
        $bucket = array_get($this->config, 'bucket');

        if (empty($tranformation)) {
            $tranformationName = 'upload';
            $extension         = $file->extension;

            if ($fullPreview && $file->type !== FileTypes::TYPE_IMAGE) {
                $tranformationName = 'preview';
                $extension         = 'jpg';
            }
        } else {
            $tranformationName = $tranformation->name;
            $extension         = $tranformation->extension;
        }

        return "https://s3.{$region}.amazonaws.com/{$bucket}/{$file->id}/{$tranformationName}.{$extension}";
    }
}
