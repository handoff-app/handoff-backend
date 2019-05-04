<?php


namespace App\Contracts\Entities\Auth\JWT;


use App\Entities\Auth\JWT\Scope;
use Exception;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Support\Collection;
use UnexpectedValueException;

interface Token
{
    public function encode(): string;

    public function encodeUrlSafe(): string;

    /**
     * @param string $jwt
     * @throws UnexpectedValueException     Provided JWT was invalid
     * @throws SignatureInvalidException    Provided JWT was invalid because the signature verification failed
     * @throws BeforeValidException         Provided JWT is trying to be used before it's eligible as defined by 'nbf'
     * @throws BeforeValidException         Provided JWT is trying to be used before it's been created as defined by 'iat'
     * @throws ExpiredException             Provided JWT has since expired, as defined by the 'exp' claim
     * @throws Exception
     * @return Token
     */
    public static function fromTokenString(string $jwt): Token;

    /**
     * @param string $jwt
     * @throws UnexpectedValueException     Provided JWT was invalid
     * @throws SignatureInvalidException    Provided JWT was invalid because the signature verification failed
     * @throws BeforeValidException         Provided JWT is trying to be used before it's eligible as defined by 'nbf'
     * @throws BeforeValidException         Provided JWT is trying to be used before it's been created as defined by 'iat'
     * @throws ExpiredException             Provided JWT has since expired, as defined by the 'exp' claim
     * @throws Exception
     * @return Token
     */
    public static function fromUrlSafeTokenString(string $jwt): Token;

    public function getScopes(): Collection;

    public function getSubject(): string;

    /**
     * @param Scope|string $scope
     * @return mixed
     */
    public function hasScope($scope): bool;
}
