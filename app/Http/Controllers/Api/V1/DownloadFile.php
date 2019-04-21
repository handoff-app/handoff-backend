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
     * @param string $token
     * @return Response
     */
    public function __invoke(DownloadFileRequest $request, string $token)
    {
        $fileUpload = FileUpload::where('access_token', $token)->active()->first();

        if (is_null($fileUpload)) {
            return response('Link is either expired or invalid', Response::HTTP_NOT_FOUND);
        }

        return Storage::download($fileUpload->path);

    }
}
