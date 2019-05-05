<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\FileUploadDeleted;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteFileRequest;
use App\Models\FileUpload;
use Illuminate\Http\Response;

class DeleteFile extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param DeleteFileRequest $request
     * @param FileUpload $fileUpload
     * @return void
     * @throws \Exception
     */
    public function __invoke(DeleteFileRequest $request, FileUpload $fileUpload)
    {
        $fileUpload->delete();

        event(new FileUploadDeleted($fileUpload));

        return response([], Response::HTTP_NO_CONTENT);
    }
}
