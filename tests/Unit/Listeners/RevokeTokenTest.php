<?php

namespace Tests\Unit\Listeners;

use App\Entities\Auth\JWT\Scope;
use App\Entities\Auth\JWT\Token;
use App\Events\FileDownloaded;
use App\Listeners\RevokeToken;
use App\Models\FileUpload;
use App\Services\Auth\TokenService;
use Carbon\Carbon;
use Tests\TestCase;

class RevokeTokenTest extends TestCase
{
    /** @test */
    public function itRevokesAToken()
    {
        $token = new Token('handoff', Carbon::now()->addHour()->isoFormat('X'), 'test-subject',
            collect([new Scope('test-scope')]));

        $tokenService = \Mockery::mock(TokenService::class);
        $tokenService->shouldReceive('revoke')
            ->once()
            ->with($token);

        $fileUpload = factory(FileUpload::class)->make();

        $listener = new RevokeToken($tokenService);

        $event = new FileDownloaded($fileUpload, $token);

        $listener->handle($event);
    }
}
