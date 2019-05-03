<?php

namespace App\Http\Middleware;

use App\Entities\Auth\JWT\Scope;
use App\Entities\Auth\JWT\Token;
use Closure;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class CheckJWTScopes
{
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
        // TODO: Separate validation into Trait
        if ($request->query('token')) {
            $tokenString = $this->tokenFromQuery($request);
        } else {
            $tokenString = $this->tokenFromHeader($request);
        }
        try {
            $token = Token::fromTokenString($tokenString);
        } catch (\Exception $e) {
            return response($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }

        if (! $this->tokenHasScopes($token, $scopes)) {
            return response('Token missing required scopes', Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }

    private function tokenFromHeader(Request $request)
    {
        $header = $request->header('Authorization');

        return substr($header, 7);
    }

    private function tokenFromQuery(Request $request)
    {
        return JWT::urlsafeB64Decode($request->query('token'));
    }

    private function tokenHasScopes(\App\Contracts\Entities\Auth\JWT\Token $token, array $scopes): bool
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
