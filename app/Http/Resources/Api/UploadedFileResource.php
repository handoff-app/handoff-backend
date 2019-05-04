<?php

namespace App\Http\Resources\Api;

use App\Contracts\Services\Auth\FileUploadTokenService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UploadedFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param FileUploadTokenService $fileUploadTokenService
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function toArray($request)
    {
        $fileUploadTokenService = resolve(FileUploadTokenService::class);

        $downloadToken = $fileUploadTokenService->forDownload($this->resource);

        $deleteToken = $fileUploadTokenService->forDelete($this->resource);

        return [
            'download_uri' => route('api.v1.download-file', [$this->resource->uuid, 'token' => $downloadToken]),
            'delete_uri' => "http://www.example.test/delete?token={$deleteToken}",
        ];
    }
}
