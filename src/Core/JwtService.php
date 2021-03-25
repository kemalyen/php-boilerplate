<?php


namespace App\Core;


use Firebase\JWT\JWT;
use Psr\Container\ContainerInterface;

class JwtService
{
    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function generate($uid)
    {
        $date = new \DateTimeImmutable();

        $secretKey  = $this->container->get('secret_key');
        $serverName = $this->container->get('app_url');

        $issuedAt = $date->getTimestamp();
        $expire = $date->modify('+15 days')->getTimestamp();      // Add 60 seconds

        $data = [
            'iat' => $issuedAt,         // Issued at: time when the token was generated
            'iss' => $serverName,       // Issuer
            'nbf' => $issuedAt,         // Not before
            'exp' => $expire,           // Expire
            'uid' => $uid,     // User name
        ];

        $token = JWT::encode(
            $data,
            $secretKey,
            'HS512'
        );
        return $token;
    }
}