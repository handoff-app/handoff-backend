<?php


namespace App\Traits\Http;


use App\Entities\Auth\JWT\Token;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

trait ResolveToken
{
    /**
     * @param Request $request
     * @return Token
     * @throws Exception
     */
    public function resolveTokenFromRequest(Request $request): \App\Contracts\Entities\Auth\JWT\Token
    {
        // TODO: Separate validation into Trait
        if ($request->query('token')) {
            $tokenString = JWT::urlsafeB64Decode($request->query('token'));
        } else {
            $header = $request->header('Authorization');
            $tokenString = substr($header, 7);
        }

        return Token::fromTokenString($tokenString);
    }
}
