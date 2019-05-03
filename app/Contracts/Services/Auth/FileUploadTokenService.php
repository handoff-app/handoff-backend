<?php


namespace App\Contracts\Services\Auth;


use App\Models\FileUpload;

interface FileUploadTokenService
{
    /**
     * @param FileUpload $fileUpload
     * @return string
     */
    public function forDownload(FileUpload $fileUpload): string;

    /**
     * @param FileUpload $fileUpload
     * @return string
     */
    public function forDelete(FileUpload $fileUpload): string;
}
