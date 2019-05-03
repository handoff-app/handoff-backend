<?php


namespace App\Contracts\Http;


use App\Contracts\Entities\Auth\JWT\Token;
use Exception;
use Illuminate\Http\Request;

interface ResolvesToken
{
    /**
     * @param Request $request
     * @return Token
     * @throws Exception
     */
    public function resolveTokenFromRequest(Request $request);
}
