<?php


namespace App\Services\Auth;


use App\Contracts\Services\Auth\FileUploadTokenService as FileUploadTokenServiceContract;
use App\Entities\Auth\JWT\Scope;
use App\Entities\Auth\JWT\Token;
use App\Models\FileUpload;
use Carbon\Carbon;

class FileUploadTokenService implements FileUploadTokenServiceContract
{
    /**
     * @param FileUpload $fileUpload
     * @return string
     * @throws \Exception
     */
    public function forDownload(FileUpload $fileUpload): string
    {
        return (new Token(
            'handoff',
            Carbon::now()->addHour()->isoFormat('X'),
            $fileUpload->uuid,
            collect([new Scope('FileUpload-view')])
        ))
            ->encodeUrlSafe();
    }

    /**
     * @param FileUpload $fileUpload
     * @return string
     * @throws \Exception
     */
    public function forDelete(FileUpload $fileUpload): string
    {
        return (new Token(
            'handoff',
            Carbon::now()->addYear()->isoFormat('X'),
            $fileUpload->uuid,
            collect([new Scope('FileUpload-delete')])
        ))
            ->encodeUrlSafe();
    }
}
