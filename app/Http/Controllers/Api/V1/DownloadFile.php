<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\DownloadFileRequest;
use App\Models\FileUpload;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class DownloadFile extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param DownloadFileRequest $request
     * @param FileUpload $fileUpload
     * @return Response
     */
    public function __invoke(DownloadFileRequest $request, FileUpload $fileUpload)
    {

        return Storage::download($fileUpload->path);

    }
}
