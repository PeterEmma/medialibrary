<?php

namespace CipeMotion\Medialibrary\Jobs;

use Storage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use CipeMotion\Medialibrary\Entities\Attachable;

class DeleteFileJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * The file id.
     *
     * @var string
     */
    protected $id;

    /**
     * The file disk.
     *
     * @var string
     */
    protected $disk;

    /**
     * Create a new file deleter job.
     *
     * @param string $id
     * @param string $disk
     */
    public function __construct($id, $disk)
    {
        $this->id   = $id;
        $this->disk = $disk;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Attachable::forFile($this->id)->delete();

        Storage::disk($this->disk)->deleteDirectory($this->id);
    }
}
