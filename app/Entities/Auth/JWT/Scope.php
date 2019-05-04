<?php


namespace App\Entities\Auth\JWT;


class Scope
{
    /**
     * @var string
     */
    private $value;

    /**
     * Scope constructor.
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function value()
    {
        return $this->value;
    }

    static public function fromString(string $scope): Scope
    {
        return new self($scope);
    }

    public function __toString()
    {
        return $this->value;
    }
}
