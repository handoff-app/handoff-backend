<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\FileDownloaded;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\DownloadFileRequest;
use App\Models\FileUpload;
use Exception;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadFile extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param DownloadFileRequest $request
     * @param FileUpload $fileUpload
     * @return StreamedResponse
     * @throws Exception
     */
    public function __invoke(DownloadFileRequest $request, FileUpload $fileUpload)
    {
        return response()->streamDownload(function () use ($fileUpload, $request) {
            $fileStream = Storage::disk($fileUpload->disk)->readStream($fileUpload->path);
            fpassthru($fileStream);
            fclose($fileStream);
            event(new FileDownloaded($fileUpload, $request->resolveToken()));
        },
            basename($fileUpload->path),
            [
                'Cache-Control' => 'no-cache',
                'Content-Type' => Storage::mimeType($fileUpload->path),
                'Content-Length' => Storage::size($fileUpload->path),
            ],
            'attachment'
        );
    }
}
