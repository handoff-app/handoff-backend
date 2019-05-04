<?php

namespace Tests\Feature\Services\Auth;

use App\Entities\Auth\JWT\Scope;
use App\Entities\Auth\JWT\Token;
use App\Services\Auth\TokenService;
use Carbon\Carbon;
use Illuminate\Cache\Repository;
use Tests\TestCase;

class TokenServiceTest extends TestCase
{
    /** @test */
    public function itRevokesTokens()
    {
        $token = new Token(
            'test',
            Carbon::now()->addHour()->isoFormat('X'),
            'test-token',
            collect([new Scope('test-scope')])
        );

        $cache = \Mockery::mock(Repository::class);


        $cache->shouldReceive('put')
              ->withArgs(function ($key, $value, $ttl) use ($token) {
                  $tokenExpiresAt = Carbon::createFromIsoFormat('X', $token->getExpiresAt())->addMinutes(15);
                  $expectedTtl = Carbon::now()->diffInSeconds($tokenExpiresAt);
                  return $key === TokenService::CACHE_KEY . "/revoked/{$token->getTokenId()}"
                      && $value === $token->getTokenId()
                      // TTL should be within 20 seconds - preventing off-by-one failures in testing
                      && (abs($expectedTtl - $ttl) < 20);
              })
              ->once();

        $tokenService = new TokenService($cache);

        $tokenService->revoke($token);
    }

    /** @test */
    public function itReturnsTrueIfTokenRevoked()
    {
        $token = new Token(
            'test',
            Carbon::now()->addHour()->isoFormat('X'),
            'test-token',
            collect([new Scope('test-scope')])
        );

        $cache = \Mockery::mock(Repository::class);

        $tokenKey = TokenService::CACHE_KEY . "/revoked/{$token->getTokenId()}";

        $cache->shouldReceive('get')
              ->with($tokenKey)
              ->andReturnTrue()
              ->once();

        $tokenService = new TokenService($cache);

        $this->assertTrue($tokenService->isRevoked($token));
    }

    /** @test */
    public function itReturnsFalseIfTokenNotRevoked()
    {
        $token = new Token(
            'test',
            Carbon::now()->addHour()->isoFormat('X'),
            'test-token',
            collect([new Scope('test-scope')])
        );

        $cache = \Mockery::mock(Repository::class);

        $tokenKey = TokenService::CACHE_KEY . "/revoked/{$token->getTokenId()}";

        $cache->shouldReceive('get')
              ->with($tokenKey)
              ->andReturnFalse()
              ->once();

        $tokenService = new TokenService($cache);

        $this->assertFalse($tokenService->isRevoked($token));
    }
}
