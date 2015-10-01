<?php

namespace CipeMotion\Medialibrary\Jobs;

use Storage;
use CipeMotion\Medialibrary\Entities\File;
use Illuminate\Contracts\Bus\SelfHandling;

abstract class TransformFileJob extends Job implements SelfHandling
{
    /**
     * The file to transform.
     *
     * @var \CipeMotion\Medialibrary\Entities\File
     */
    protected $file;

    /**
     * The transformation name.
     *
     * @var string
     */
    protected $name;

    /**
     * The transformer class.
     *
     * @var string
     */
    protected $transformer;

    /**
     * The transformer config.
     *
     * @var array
     */
    protected $config;

    /**
     * Create a transform job.
     *
     * @param \CipeMotion\Medialibrary\Entities\File $file
     * @param string                                 $transformer
     * @param array                                  $config
     */
    public function __construct(File $file, $name, $transformer, array $config)
    {
        $this->file        = $file;
        $this->name        = $name;
        $this->transformer = $transformer;
        $this->config      = $config;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        /** @var \CipeMotion\Medialibrary\Transformers\ITransformer $transformer */
        $transformer = app($this->transformer, [$this->name, $this->config]);

        $transformation = $transformer->transform($this->file);

        $this->file->transformations()->save($transformation);
    }
}