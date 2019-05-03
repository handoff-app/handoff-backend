<?php

namespace Tests\Feature\Api\V1;

use App\Entities\Auth\JWT\Scope;
use App\Entities\Auth\JWT\Token;
use App\Models\FileUpload;
use Carbon\Carbon;
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

        $token = new Token('testing', Carbon::now()->addMinute()->isoFormat('X'), $fileUpload->uuid,
            collect([new Scope('FileUpload-view')]));

        $response = $this->get(route('api.v1.download-file', [$fileUpload->uuid, 'token' => $token->encodeUrlSafe()]));
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

        $token = new Token(
            'testing',
            Carbon::now()->subMinutes(1)->isoFormat('X'),
            $fileUpload->uuid,
            collect([new Scope('FileUpload-view')]),
            Carbon::now()->subMinutes(2)->isoFormat('X')
        );

        $response = $this->get(route('api.v1.download-file', [$fileUpload->uuid, 'token' => $token->encodeUrlSafe()]));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function itBlocksAccessToLinksWithoutTokens()
    {
        Storage::fake();

        $file = UploadedFile::fake()->create('fake-file.sql', 1024);

        $path = Storage::putFile('uploads', $file);

        $fileUpload = factory(FileUpload::class)
            ->state('expired')
            ->create([
                'path' => $path,
            ]);

        $response = $this->get(route('api.v1.download-file', [$fileUpload->uuid]));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
