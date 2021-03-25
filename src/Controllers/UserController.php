<?php declare (strict_types=1);

namespace App\Controllers;

use App\Core\JwtService;
use App\Repositories\UserRepository;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Valitron\Validator;

class UserController
{
    /**
     * @Inject
     * @var UserRepository
     */
    private UserRepository $userRepository;

    public function user(ServerRequestInterface $request): JsonResponse
    {
        $uid = $request->getAttribute('uid');
        $user = $this->userRepository->findOneByUid($uid);

        $roles = $user->getRoles();
        $roleList = [];
        foreach ($roles as $role){
            $roleList[] = $role->getDisplayName();
        }

        return new JsonResponse(['email' => $user->getEmail(), 'roles' => $roleList], 200, ['Content-Type' => ['application/hal+json']]);
    }


    public function update(ServerRequestInterface $request): JsonResponse
    {
        $uid = $request->getAttribute('uid');
        $user = $this->userRepository->findOneByUid($uid);

        $parsedBody = $request->getParsedBody();
        $v = new Validator($parsedBody);
        $v->rule('required', ['email', 'password']);
        $v->rule('email', ['email']);

        if (!$v->validate()) {
            return new JsonResponse($v->errors(), 422, ['Content-Type' => ['application/hal+json']]);
        }

        if(($user->getEmail() != $parsedBody['email']) && ($this->userRepository->findOneByEmail($parsedBody['email']) != null)) {
            return new JsonResponse(['message' => 'The email has already been used!'], 422, ['Content-Type' => ['application/hal+json']]);
        }

        $this->userRepository->update($parsedBody, $user );
        return new JsonResponse(['message' => 'User updated!'], 201, ['Content-Type' => ['application/hal+json']]);
    }
}