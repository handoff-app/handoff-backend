<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UploadedFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'download_uri' => "http://www.example.test/download?access_token={$this->resource->access_token}",
            'delete_uri' => "http://www.example.test/delete?access_token={$this->resource->access_token}",
        ];
    }
}
