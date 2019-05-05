<?php


namespace App\Contracts\Http;


use App\Entities\Auth\JWT\Token;
use Illuminate\Http\Request;

interface ResolvesToken
{
    /**
     * @param Request $request
     * @return Token
     */
    public function resolveToken(?Request $request): Token;
}
