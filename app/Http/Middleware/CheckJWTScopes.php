<?php

namespace App\Http\Middleware;

use App\Contracts\Http\ResolvesToken;
use App\Entities\Auth\JWT\Scope;
use App\Entities\Auth\JWT\Token;
use App\Traits\Http\ResolveToken;
use Closure;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class CheckJWTScopes implements ResolvesToken
{
    use ResolveToken;
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param array $scopes
     * @return mixed
     */
    public function handle($request, Closure $next, ...$scopes)
    {
        try {
            $token = $this->resolveTokenFromRequest($request);
        } catch (\Exception $e) {
            return response($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }

        if (! $this->tokenHasScopes($token, $scopes)) {
            return response('Token missing required scopes', Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }

    private function tokenHasScopes(Token $token, array $scopes): bool
    {
        $tokenScopes = $token->getScopes();

        $middlewareScopes = Collection::make($scopes)
                                      ->mapInto(Scope::class);

        if ($tokenScopes->count() !== $middlewareScopes->count()) {
            return false;
        }

        return $middlewareScopes->every(function ($scope) use ($tokenScopes) {
            return $tokenScopes->contains($scope);
        });
    }
}
