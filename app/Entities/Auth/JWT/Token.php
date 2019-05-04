<?php


namespace App\Entities\Auth\JWT;


use Carbon\Carbon;
use Exception;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use UnexpectedValueException;

class Token
{
    /** @var Collection */
    protected $scopes;
    /**
     * @var string
     */
    protected $issuer;
    /**
     * @var string
     */
    protected $expiresAt;
    /**
     * @var string
     */
    protected $subject;
    /**
     * @var string
     */
    protected $issuedAt;
    /**
     * @var string
     */
    protected $notBefore;
    /**
     * @var string
     */
    protected $tokenId;

    /**
     * Token constructor.
     * @param string $issuer
     * @param string $expiresAt
     * @param string $subject
     * @param Collection $scopes
     * @param string $issuedAt
     * @param string $notBefore
     * @param string $tokenId
     * @throws Exception
     */
    public function __construct(
        string $issuer,
        string $expiresAt,
        string $subject,
        Collection $scopes,
        ?string $issuedAt = null,
        ?string $notBefore = null,
        ?string $tokenId = null
    ) {
        if ($scopes->count() === 0) {
            throw new InvalidArgumentException("You need to attach scopes to a token");
        }

        $scopes->each(function ($scope) {
            if (get_class($scope) !== Scope::class) {
                throw new InvalidArgumentException(get_class($scope) . " is not an instance of " . Scope::class);
            }
        });

        $this->scopes = $scopes;
        $this->issuer = $issuer;
        $this->expiresAt = $expiresAt;
        $this->subject = $subject;
        $this->issuedAt = $issuedAt ?? Carbon::now()->isoFormat('X') ;
        $this->notBefore = $notBefore ?? $this->issuedAt;
        $this->tokenId = $tokenId ?? Uuid::uuid4()->toString();
    }

    public function encode(): string
    {
        return JWT::encode($this->payload(), config('auth.jwt.secret'), 'HS256');
    }

    public function encodeUrlSafe(): string
    {
        return JWT::urlsafeB64Encode($this->encode());
    }

    /**
     * @param string $jwt
     * @return TokenContract
     * @throws UnexpectedValueException     Provided JWT was invalid
     * @throws SignatureInvalidException    Provided JWT was invalid because the signature verification failed
     * @throws BeforeValidException         Provided JWT is trying to be used before it's eligible as defined by 'nbf'
     * @throws BeforeValidException         Provided JWT is trying to be used before it's been created as defined by 'iat'
     * @throws ExpiredException             Provided JWT has since expired, as defined by the 'exp' claim
     * @throws Exception
     */
    public static function fromTokenString(string $jwt): Token
    {
        $data = JWT::decode($jwt, config('auth.jwt.secret'), ['HS256']);

        if (! isset($data->scopes) || ! is_array($data->scopes)) {
            throw new InvalidArgumentException("No scopes set or scopes isn't an array");
        }

        $scopes = Collection::make($data->scopes)->mapInto(Scope::class);

        return new self(
            $data->iss,
            $data->exp,
            $data->sub,
            $scopes,
            $data->iat,
            $data->nbf,
            $data->jti
        );
    }

    public function getScopes(): Collection
    {
        return $this->scopes;
    }

    public function payload(): array
    {
        $mappedScopes = $this->getScopes()->map(function (Scope $scope) {
            return $scope->value();
        })
            ->toArray();
        $scopes = ['scopes' => $mappedScopes];

        $baseClaims = Collection::make([
            'iss' => $this->issuer,
            'iat' => $this->issuedAt,
            'exp' => $this->expiresAt,
            'nbf' => $this->notBefore,
            'sub' => $this->subject,
            'jti' => $this->tokenId,
        ]);

        return $baseClaims->merge($scopes)->toArray();
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param Scope|string $scope
     * @return mixed
     */
    public function hasScope($scope): bool
    {
        $value = is_string($scope) ? $scope : $scope->value();

        return $this->scopes->some(function (Scope $scope) use ($value) {
            return $scope->value() === $value;
        });
    }

    public static function fromUrlSafeTokenString(string $jwt): Token
    {
        return self::fromTokenString(JWT::urlsafeB64Decode($jwt));
    }

    /**
     * @return string
     */
    public function getTokenId(): string
    {
        return $this->tokenId;
    }

    /**
     * @return string
     */
    public function getExpiresAt(): string
    {
        return $this->expiresAt;
    }
}
