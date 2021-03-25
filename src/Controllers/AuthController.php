<?php declare (strict_types=1);

namespace App\Controllers;

use App\Core\JwtService;
use App\Repositories\UserRepository;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Valitron\Validator;

class AuthController
{
    /**
     * @Inject
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @Inject
     * @var JwtService
     */
    private JwtService $jwtService;

    public function signup(ServerRequestInterface $request): JsonResponse
    {
        $parsedBody = $request->getParsedBody();
        $v = new Validator($parsedBody);
        $v->rule('required', ['email', 'password']);
        $v->rule('email', ['email']);

        if (!$v->validate()) {
            return new JsonResponse($v->errors(), 422, ['Content-Type' => ['application/hal+json']]);
        }

        if ($this->userRepository->findOneByEmail($parsedBody['email']) != null) {
            return new JsonResponse(['message' => 'The email has already been used!'], 422, ['Content-Type' => ['application/hal+json']]);
        }

        $uid = uniqid();
        $this->userRepository->create($parsedBody, $uid);
        return new JsonResponse(['message' => 'Account created!'], 201, ['Content-Type' => ['application/hal+json']]);
    }

    public function signin(ServerRequestInterface $request): JsonResponse
    {
        $parsedBody = $request->getParsedBody();
        $v = new Validator($parsedBody);
        $v->rule('required', ['email', 'password']);
        $v->rule('email', ['email']);

        if (!$v->validate()) {
            return new JsonResponse($v->errors(), 422, ['Content-Type' => ['application/hal+json']]);
        }
        try {
            $user = $this->userRepository->findOneByEmail($parsedBody['email']);
            if (password_verify($parsedBody['password'], $user->getPassword())) {
                $token = $this->jwtService->generate($user->getUid());
                return new JsonResponse(['token' => $token], 200, ['Content-Type' => ['application/hal+json']]);
            }
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Login failed, please check your credentials'], 422, ['Content-Type' => ['application/hal+json']]);
        }
        return new JsonResponse(['message' => 'Login failed, please check your credentials'], 422, ['Content-Type' => ['application/hal+json']]);
    }
}