<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserController extends AbstractController
{
    #[Route('/api/v1/users/pagination', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns list of users',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class))
        )
    )]
    #[OA\Tag(name: 'Users')]
    #[Security(name: 'Bearer')]
    public function pagination(UserRepository $repository)
    {
        $users = $repository->findAllWithPagination(1, 10);

        return $this->json($users);
    }

    #[Route('/api/v1/users/list', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns list of users',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class))
        )
    )]
    #[OA\Tag(name: 'Users')]
    #[Security(name: 'Bearer')]
    public function list(UserRepository $repository)
    {
        $users = $repository->findAllUsers();

        return $this->json($users);
    }

    #[Route('/api/v1/users/{id}', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns a single user by id',
        content: new Model(type: User::class)
    )]
    #[OA\Response(response: 404, description: 'User not found')]
    #[OA\Tag(name: 'Users')]
    #[Security(name: 'Bearer')]
    public function show(UserRepository $repository, int $id)
    {
        $user = $repository->findOneById($id);

        if (!$user) {
            return $this->json(['message' => 'User not found'], 404);
        }

        return $this->json($user);
    }

    #[Route('/api/v1/users', methods: ['POST'])]
    #[OA\RequestBody(
        description: 'User data for creating a new user',
        required: true,
        content: new OA\JsonContent(
            required: ['username', 'email', 'password'],
            properties: [
                new OA\Property(property: 'username', type: 'string'),
                new OA\Property(property: 'email', type: 'string'),
                new OA\Property(property: 'password', type: 'string'),
                new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string'), nullable: true),
            ]
        )
    )]
    #[OA\Response(response: 201, description: 'User created')]
    #[OA\Response(response: 400, description: 'Invalid input')]
    #[OA\Tag(name: 'Users')]
    #[Security(name: 'Bearer')]
    public function create(Request $request, UserRepository $repository, UserPasswordHasherInterface $passwordHasher)
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            return $this->json(['message' => 'Missing required fields'], 400);
        }

        $user = new User();
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);
        $user->setRoles($data['roles'] ?? []);

        $repository->save($user);

        return $this->json($user, 201);
    }

    #[Route('/api/v1/users/{id}', methods: ['PUT'])]
    #[OA\RequestBody(
        description: 'User data to update',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'username', type: 'string', nullable: true),
                new OA\Property(property: 'email', type: 'string', nullable: true),
                new OA\Property(property: 'password', type: 'string', nullable: true),
                new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string'), nullable: true),
            ]
        )
    )]
    #[OA\Response(response: 200, description: 'User updated')]
    #[OA\Response(response: 404, description: 'User not found')]
    #[OA\Tag(name: 'Users')]
    #[Security(name: 'Bearer')]
    public function update(
        int $id,
        Request $request,
        UserRepository $repository,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $user = $repository->findOneById($id);

        if (!$user) {
            return $this->json(['message' => 'User not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['username'])) {
            $user->setUsername($data['username']);
        }

        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }

        if (!empty($data['password'])) {
            $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
        }

        if (isset($data['roles']) && is_array($data['roles'])) {
            $user->setRoles($data['roles']);
        }

        $repository->save($user);

        return $this->json($user);
    }

    #[Route('/api/v1/users/{id}', methods: ['DELETE'])]
    #[OA\Response(response: 204, description: 'User deleted')]
    #[OA\Response(response: 404, description: 'User not found')]
    #[OA\Tag(name: 'Users')]
    #[Security(name: 'Bearer')]
    public function delete(int $id, UserRepository $repository)
    {
        $user = $repository->findOneById($id);

        if (!$user) {
            return $this->json(['message' => 'User not found'], 404);
        }

        $repository->remove($user);

        return $this->json(null, 204);
    }


}
