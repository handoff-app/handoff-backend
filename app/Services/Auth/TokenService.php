<?php


namespace App\Services\Auth;


use App\Contracts\Services\Auth\TokenService as TokenServiceContract;
use App\Entities\Auth\JWT\Token;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\Repository;

class TokenService implements TokenServiceContract
{
    const CACHE_KEY = 'Tokens';
    /**
     * @var Repository
     */
    private $cache;

    /**
     * TokenService constructor.
     * @param Repository $cache
     */
    public function __construct(Repository $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param Token $token
     */
    public function revoke(Token $token): void
    {
        $tokenExpiresAt = Carbon::createFromIsoFormat('X', $token->getExpiresAt())->addMinutes(15);
        $ttl = Carbon::now()->diffInSeconds($tokenExpiresAt);
        $this->cache->put($this->revokedCacheKey($token), $token->getTokenId(), $ttl);
    }

    /**
     * @param Token $token
     * @return bool
     */
    public function isRevoked(Token $token): bool
    {
        return true == $this->cache->get($this->revokedCacheKey($token));
    }

    private function revokedCacheKey(Token $token): string
    {
        return self::CACHE_KEY . "/revoked/{$token->getTokenId()}";
    }
}
