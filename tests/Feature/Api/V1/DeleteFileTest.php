<?php

namespace Tests\Feature\Api\V1;

use App\Entities\Auth\JWT\Scope;
use App\Entities\Auth\JWT\Token;
use App\Jobs\DeleteFileUpload;
use App\Models\FileUpload;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DeleteFileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itDeletesTheFileAndSoftDeletesRecord()
    {
        Storage::fake();

        $file = UploadedFile::fake()->create('test.sql', 500);

        $fileUpload = factory(FileUpload::class)->create([
            'path' => $file->path(),
        ]);

        Storage::put($file->path(), $file);

        $token = new Token('test', Carbon::now()->addHour()->isoFormat('X'), $fileUpload->uuid,
            collect([new Scope('FileUpload-delete')]));

        try {
            Storage::get($file->path());
        } catch (\Exception $e) {
            $this->fail('File was not stored');
        }

        $response = $this->delete(route('api.v1.delete-file', [$fileUpload->uuid, 'token' => $token->encodeUrlSafe()]));

        $response->assertStatus(204);

        $this->assertDatabaseMissing('file_uploads', [
            'id' => $fileUpload->id,
            'deleted_at' => null,
        ]);

        try {
            Storage::get($file->path());
            $this->fail('File was not deleted');
        } catch (\Exception $e) {
            // Expected
        }
    }

    /** @test */
    public function itDeletesTheFileAndSoftDeletesRecordFromAction()
    {
        Storage::fake();

        $file = UploadedFile::fake()->create('test.sql', 500);

        $fileUpload = factory(FileUpload::class)->create([
            'path' => $file->path(),
        ]);

        Storage::put($file->path(), $file);

        $token = new Token('test', Carbon::now()->addHour()->isoFormat('X'), $fileUpload->uuid,
            collect([new Scope('FileUpload-delete')]));

        try {
            Storage::get($file->path());
        } catch (\Exception $e) {
            $this->fail('File was not stored');
        }

        $response = $this->get(route('api.v1.delete-file-action', [$fileUpload->uuid, 'token' => $token->encodeUrlSafe()]));

        $response->assertStatus(204);

        $this->assertDatabaseMissing('file_uploads', [
            'id' => $fileUpload->id,
            'deleted_at' => null,
        ]);

        try {
            Storage::get($file->path());
            $this->fail('File was not deleted');
        } catch (\Exception $e) {
            // Expected
        }
    }
}
