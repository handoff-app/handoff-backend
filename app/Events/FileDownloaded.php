<?php

namespace App\Events;

use App\Entities\Auth\JWT\Token;
use App\Models\FileUpload;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FileDownloaded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * @var FileUpload
     */
    public $file;
    /**
     * @var Token
     */
    public $token;

    /**
     * Create a new event instance.
     *
     * @param FileUpload $file
     * @param Token $token
     */
    public function __construct(FileUpload $file, Token $token)
    {
        $this->file = $file;
        $this->token = $token;
        Log::info("File {$file->path} has been downloaded at " . now());
    }
}
