<?php

namespace Tests\Unit\Models;

use App\Models\FileUpload;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FileUploadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itHasExpiredScope()
    {
        factory(FileUpload::class)->times(5)->create();

        $expired = factory(FileUpload::class)->state('expired')->times(3)->create();

        /** @var Collection $expiredModels */
        $expiredModels = FileUpload::expired()->get();

        $this->assertEquals($expired->count(), $expiredModels->count());
    }
}
