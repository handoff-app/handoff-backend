<?php


namespace App\Traits\Http;


use App\Entities\Auth\JWT\Token;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

trait ResolveToken
{
    /**
     * @param Request|null $request
     * @return Token
     * @throws Exception
     */
    public function resolveToken(?Request $request = null): Token
    {
        $requestInstance = $request ?? $this;
        if (!($requestInstance instanceof Request)) {
            throw new Exception('The ResolveToken trait can only be used on instances of ' . Request::class);
        }

        if ($requestInstance->query('token')) {
            $tokenString = JWT::urlsafeB64Decode($requestInstance->query('token'));
        } else {
            $header = $requestInstance->header('Authorization');
            $tokenString = substr($header, 7);
        }

        return Token::fromTokenString($tokenString);
    }
}
