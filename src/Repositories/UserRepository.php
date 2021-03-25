<?php
declare(strict_types=1);

namespace App\Repositories;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use App\Entities\Role;
use App\Entities\User;
use App\Exceptions\DoctrineValidationException;

class UserRepository
{
    /**
     * @var EntityRepository
     */
    private $repository;

    private $em;

    private $roleRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->repository = $entityManager->getRepository(User::class);
        $this->roleRepository = $entityManager->getRepository(Role::class);
        $this->em = $entityManager;
    }

    public function create($data, $uid)
    {
        $roles = $this->parseRoles($data);
        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword($data['password']);
        $user->setUid($uid);
        foreach ($roles as $role){
            $roleModel = $this->getRoleByName($role);
            if ($roleModel){
                $user->addRole($roleModel);
            }
        }

        $this->save($user);
    }


    public function update($data, $user)
    {
        $roles = $this->parseRoles($data);

        $user->setEmail($data['email']);
        $user->setPassword($data['password']);
        if (count($roles) > 0){
            $user->removeRoles();
            foreach ($roles as $role){
                $roleModel = $this->getRoleByName($role);
                if ($roleModel){
                    $user->addRole($roleModel);
                }
            }
        }


        $this->save($user);
    }

    private function parseRoles($parsedBody)
    {
        If(!empty($parsedBody['roles'])){
            $data = $parsedBody['roles'];
        }

        $roles = [];
        foreach ($data as $datum) {
            if (isset($roles[$datum])){
                $roles[] =  $datum;
            }
        }
        return $roles;
    }

    public function getRoleByName($name)
    {
        return $this->roleRepository->findOneBy(['name' => $name]);
    }

    public function findOneByEmail(string $email)
    {
        return $this->repository->findOneBy(['email' => $email]);
    }

    public function findOneByUid(string $uid)
    {
        return $this->repository->findOneBy(['uid' => $uid]);
    }

    private function save(User $user)
    {
        try {
            $this->em->persist($user);
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new DoctrineValidationException($e->getMessage());
        }
    }

}