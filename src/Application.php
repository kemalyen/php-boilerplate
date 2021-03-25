<?php
namespace App;

use App\Exceptions\TokenNotFoundException;
use Laminas\Diactoros\Response\JsonResponse;
use League\Route\Http\Exception\NotFoundException;
use App\Exceptions\NotFoundException as AppNotFoundException;
class Application
{
    public $router;

    protected $response;

    public function __construct() {

    }    
 
    public function router(\League\Route\Router $router){
        $this->router = $router;
    }

    public function process(\Psr\Http\Message\ServerRequestInterface $request)
    {
        try {
            $this->response = $this->router->dispatch($request);
        }catch (\Exception $exception){
            $this->response =  new JsonResponse(['File not found'], 404, ['Content-Type' => ['application/hal+json']]);
        }
    }

    public function run()
    {
        (new \Zend\HttpHandlerRunner\Emitter\SapiEmitter)->emit($this->response);        
    }
}