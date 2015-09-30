<?php

namespace CipeMotion\Medialibrary\Observers;

use Storage;
use Rhumsaa\Uuid\Uuid;
use CipeMotion\Medialibrary\Entities\File;
use Illuminate\Foundation\Bus\DispatchesJobs;
use CipeMotion\Medialibrary\Jobs\DeleteFileJob;
use CipeMotion\Medialibrary\Jobs\TransformFileQueuedJob;
use CipeMotion\Medialibrary\Jobs\TransformFileUnqueuedJob;

class FileObserver
{
    use DispatchesJobs;

    public function creating(File $file)
    {
        if (is_null($file->getAttribute('id'))) {
            $file->id = Uuid::uuid4()->toString();
        }
    }

    public function created(File $file)
    {
        $transformers = config("medialibrary.file_types.{$file->type}.transformations");

        foreach ($transformers as $name => $transformer) {
            $queue = array_get($transformer, 'queued');

            if ($queue === false) {
                $job = new TransformFileUnqueuedJob(
                    $file,
                    $name,
                    array_get($transformer, 'transformer'),
                    array_get($transformer, 'config', [])
                );
            } else {
                $job = new TransformFileQueuedJob(
                    $file,
                    $name,
                    array_get($transformer, 'transformer'),
                    array_get($transformer, 'config', [])
                );

                if (is_string($queue)) {
                    $job->onQueue($job);
                }
            }

            $this->dispatch($job);
        }
    }

    public function deleted(File $file)
    {
        $this->dispatch(new DeleteFileJob($file->id, $file->disk));
    }
}
