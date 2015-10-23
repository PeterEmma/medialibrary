<?php

namespace CipeMotion\Medialibrary\Generators;

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
     *
     * @return string
     */
    public function getUrlForTransformation(File $file, Transformation $tranformation = null)
    {
        $region        = array_get($this->config, 'region');
        $bucket        = array_get($this->config, 'bucket');
        $tranformation = (empty($tranformation)) ? 'upload' : $tranformation->name;

        return "https://s3.{$region}.amazonaws.com/{$bucket}/{$file->id}/{$tranformation}.{$file->extension}";
    }
}
