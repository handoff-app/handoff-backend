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
     * @todo Maybe make this method only usable on requests so it can just use `$this`
     */
    public function resolveTokenFromRequest(Request $request): Token
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
