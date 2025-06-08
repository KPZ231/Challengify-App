<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use stdClass;

class JwtService
{
    private string $secret;
    private int $expiration;
    private string $algorithm;

    public function __construct(array $config)
    {
        $this->secret = $config['jwt']['secret'];
        $this->expiration = $config['jwt']['expiration'];
        $this->algorithm = $config['jwt']['algorithm'];
    }

    public function generate(array $payload): string
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + $this->expiration;

        $tokenPayload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'data' => $payload
        ];

        return JWT::encode($tokenPayload, $this->secret, $this->algorithm);
    }

    public function validate(string $token): ?stdClass
    {
        try {
            return JWT::decode($token, new Key($this->secret, $this->algorithm));
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getPayloadFromToken(string $token): ?array
    {
        $payload = $this->validate($token);
        
        if ($payload === null) {
            return null;
        }
        
        return (array) $payload->data;
    }
} 