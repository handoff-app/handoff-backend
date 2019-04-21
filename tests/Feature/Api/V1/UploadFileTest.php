<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadFileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itUploadsAFileAndReturnsInformation()
    {
        Storage::fake();

        $file = UploadedFile::fake()->create('database.sql', 1024);

        $response = $this->postJson(
            route('api.v1.upload-file'),
            [
                'file' => $file,
            ]
        );

        Storage::disk()->assertExists("uploads/{$file->hashName()}");

        $response->assertJsonStructure([
            'data' => [
                'download_uri',
                'delete_uri',
            ],
        ]);

    }
}
