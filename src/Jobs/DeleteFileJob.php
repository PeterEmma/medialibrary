<?php

namespace CipeMotion\Medialibrary\Jobs;

use Storage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteFileJob extends Job implements SelfHandling, ShouldQueue
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
        Storage::disk($this->disk)->deleteDirectory($this->id);
    }
}
