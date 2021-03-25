<?php declare (strict_types = 1);

namespace App\Middlewares;

use App\Exceptions\TokenNotFoundException;
use App\Exceptions\UnauthorizedException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use \Firebase\JWT\JWT;
use Psr\Container\ContainerInterface;

class Auth implements MiddlewareInterface
{
    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // invoke the rest of the middleware stack and your controller resulting
        // in a returned response object
        $jwt = $this->getBearerToken($request);
        if (!$jwt){
            throw new TokenNotFoundException;
        }

        $secretKey  = $this->container->get('secret_key');
        $app_url = $this->container->get('app_url');
        $token = JWT::decode($jwt, $secretKey, ['HS512']);
        $now = new \DateTimeImmutable();

        if ($token->iss !== $app_url ||
            $token->nbf > $now->getTimestamp() ||
            $token->exp < $now->getTimestamp() ||
            !isset($token->uid))
        {
            throw new UnauthorizedException;
        }

        $request = $request->withAttribute("uid", $token->uid);
        $response = $handler->handle($request);
        // ...
        // do something with the response
        return $response;
    }

    private function getBearerToken($request): string | bool
    {
        $header = $request->getHeader('Authorization', null);

        if (is_array($header) && empty($header[0])) {
            return false;
        }
        #
        if (! preg_match('/Bearer\s(\S+)/', $header[0], $matches)) {
            return false;
        }
        return substr($header[0], 7);
    }
}