<?php

namespace Tests\Feature\Api\V1;

use App\Models\FileUpload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DownloadFileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itDownloadsFilesAndRemovesThem()
    {
        Storage::fake();

        $file = UploadedFile::fake()->create('fake-file.sql', 1024);

        $path = Storage::putFile('uploads', $file);

        $fileUpload = factory(FileUpload::class)->create([
            'path' => $path,
        ]);

        $response = $this->get(route('api.v1.download-file', ['token' => $fileUpload->access_token]));
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', "attachment; filename={$file->hashName()}");
    }

    /** @test */
    public function itBlocksAccessToExpiredLinks()
    {
        Storage::fake();

        $file = UploadedFile::fake()->create('fake-file.sql', 1024);

        $path = Storage::putFile('uploads', $file);

        $fileUpload = factory(FileUpload::class)
            ->state('expired')
            ->create([
                'path' => $path,
            ]);

        $response = $this->get(route('api.v1.download-file', ['token' => $fileUpload->access_token]));

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
