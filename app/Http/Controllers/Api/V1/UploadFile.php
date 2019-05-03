<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UploadFileRequest;
use App\Http\Resources\Api\UploadedFileResource;
use App\Models\FileUpload;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

class UploadFile extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param UploadFileRequest $request
     * @return UploadedFileResource
     * @throws Exception
     */
    public function __invoke(UploadFileRequest $request)
    {
        $path = Storage::putFile('uploads', $request->file('file'));

        $fileUpload = new FileUpload([
            'uuid' => Uuid::uuid4(),
            'path' => $path,
            'expires_at' => Carbon::now()->addHour(),
        ]);

        $fileUpload->save();

        return new UploadedFileResource($fileUpload);
    }
}
