<?php

namespace App\Events;

use App\Models\FileUpload;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FileUploadDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var string */
    public $uuid;
    /** @var string */
    public $disk;
    /** @var string */
    public $path;

    /**
     * Create a new event instance.
     *
     * @param FileUpload $fileUpload
     */
    public function __construct(FileUpload $fileUpload)
    {
        $this->uuid = $fileUpload->uuid;
        $this->disk = $fileUpload->disk;
        $this->path = $fileUpload->path;
    }

}
