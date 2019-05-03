<?php


namespace App\Contracts\Entities\Auth\JWT;


use App\Entities\Auth\JWT\Scope;
use Illuminate\Support\Collection;

interface Token
{
    public function encode(): string;

    public function encodeUrlSafe(): string;

    public static function fromTokenString(string $jwt): Token;

    public static function fromUrlSafeTokenString(string $jwt): Token;

    public function getScopes(): Collection;

    public function getSubject(): string;

    /**
     * @param Scope|string $scope
     * @return mixed
     */
    public function hasScope($scope): bool;
}
