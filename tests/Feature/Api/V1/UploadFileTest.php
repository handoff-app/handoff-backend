<?php

namespace Tests\Feature\Api\V1;

use App\Models\FileUpload;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
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

        $response->assertStatus(Response::HTTP_CREATED);

        Storage::disk()->assertExists("uploads/{$file->hashName()}");

        try {
            $fileUpload = FileUpload::where('path', 'like', "%{$file->hashName()}%")->firstOrFail();
        } catch (ModelNotFoundException $e) {
            $this->fail('File upload does not exist in database');
            return;
        }

        $response->assertJsonStructure([
            'data' => [
                'download_uri',
                'delete_uri',
            ],
        ]);

        $downloadUri = $response->json('data')['download_uri'];
        $deleteUri = $response->json('data')['delete_uri'];

        $this->assertEquals(1, preg_match('/.*\/files\/[a-zA-Z\d-]+\?token=.*$/', $downloadUri));
        $this->assertEquals(1, preg_match('/.*\/files\/[a-zA-Z\d-]+\/delete\?token=.*$/', $deleteUri));
    }
}
