<?php


namespace App\Contracts\Services\Auth;


use App\Entities\Auth\JWT\Token;

interface TokenService
{
    /**
     * @param Token $token
     */
    public function revoke(Token $token): void;

    /**
     * @param Token $token
     * @return bool
     */
    public function isRevoked(Token $token): bool;
}
