<?php declare (strict_types=1);

namespace App\Controllers;

use App\Repositories\UserRepository;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Log\LoggerInterface;

class IndexController
{
    /**
     * @Inject
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @Inject
     * @var Psr\Log\LoggerInterface
     */    
    private $logger;


    public function index(): JsonResponse
    {
        $this->logger->info('Homepage index visited');
        return new JsonResponse(['Hello World'], 200, ['Content-Type' => ['application/hal+json']]);
    }

    public function secret(ServerRequestInterface $request): JsonResponse
    {
        $uid = $request->getAttribute('uid');
        $user = $this->userRepository->findOneByUid($uid);
        return new JsonResponse(['Hello World '. $user->getEmail()], 200, ['Content-Type' => ['application/hal+json']]);
    }
}