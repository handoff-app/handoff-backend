<?php

namespace App\Listeners;

use App\Events\FileUploadDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeleteFile implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param FileUploadDeleted $event
     * @return void
     */
    public function handle(FileUploadDeleted $event)
    {
        try {
            Storage::disk($event->disk)->delete($event->path);
        } catch (\Exception $e) {
            Log::error("Failed to delete file for upload {$event->uuid}. {$e->getMessage()}");
        }
    }
}
