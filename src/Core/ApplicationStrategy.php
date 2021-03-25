<?php declare (strict_types = 1);

namespace App\Core;

use League\Route\ContainerAwareInterface;
use League\Route\ContainerAwareTrait;

use League\Route\Http\Exception\MethodNotAllowedException;

use League\Route\Http\Exception\NotFoundException;use League\Route\Route;
use League\Route\Strategy\AbstractStrategy;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;

use Throwable;

class ApplicationStrategy extends AbstractStrategy implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function invokeRouteCallable(Route $route, ServerRequestInterface $request): ResponseInterface
    {
        $controller = $route->getCallable($this->getContainer());

        $response = $controller($request, $route->getVars());

        $response = $this->applyDefaultResponseHeaders($response);

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getNotFoundDecorator(NotFoundException $exception): MiddlewareInterface
    {
        return $this->throwThrowableMiddleware($exception);
    }

    /**
     * {@inheritdoc}
     */
    public function getMethodNotAllowedDecorator(MethodNotAllowedException $exception): MiddlewareInterface
    {
        return $this->throwThrowableMiddleware($exception);
    }

    /**
     * Return a middleware that simply throws an error
     *
     * @param \Throwable $error
     *
     * @return \Psr\Http\Server\MiddlewareInterface
     */
    protected function throwThrowableMiddleware(Throwable $error): MiddlewareInterface
    {
        return new class($error) implements MiddlewareInterface
        {
            protected $error;

            public function __construct(Throwable $error)
            {
                $this->error = $error;
            }

            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $requestHandler
            ): ResponseInterface {
                throw $this->error;
            }
        };
    }

    /**
     * {@inheritdoc}
     */
    public function getExceptionHandler(): MiddlewareInterface
    {
        return $this->getThrowableHandler();
    }

    /**
     * {@inheritdoc}
     */
    public function getThrowableHandler(): MiddlewareInterface
    {
        return new class implements MiddlewareInterface
        {
            /**
             * {@inheritdoc}
             *
             * @throws Throwable
             */
            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $requestHandler
            ): ResponseInterface {
                try {
                    return $requestHandler->handle($request);
                } catch (Throwable $e) {
                     
                    return $this->handleException($request, $e);
                }
            }

            /**
             * @param ServerRequestInterface $request
             * @param Throwable              $exception
             * @return ResponseInterface
             */
            public function handleException(ServerRequestInterface $request, Throwable $exception): ResponseInterface
            {
                $data = ['message' => $exception->getMessage()];
                $code = (!empty($exception->getCode())) ? $exception->getCode() : 500; 
                $response = new JsonResponse($data, $code, ['Content-Type' => ['application/hal+json']]);

                return $response;
            }

        };
    }

}