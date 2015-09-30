<?php

namespace CipeMotion\Medialibrary\Generators;

use CipeMotion\Medialibrary\Entities\File;
use CipeMotion\Medialibrary\Entities\Transformation;

class AzureUrlGenerator implements IUrlGenerator
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
        $account       = array_get($this->config, 'account');
        $container     = array_get($this->config, 'container');
        $tranformation = (empty($tranformation)) ? 'upload' : $tranformation->name;

        return "https://{$account}.blob.core.windows.net/{$container}/{$file->id}/{$tranformation}.{$file->extension}";
    }
}
