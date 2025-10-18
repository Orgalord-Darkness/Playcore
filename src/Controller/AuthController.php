<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use OpenApi\Attributes as OA;

class AuthController
{
  

    #[Route('/api/login_check', name: 'api_login_check_doc', methods: ['POST'])]
    #[OA\Tag(name: 'Auth')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            required: ['email', 'password'],
            properties: [
                new OA\Property(property: 'email', type: 'string', example: 'admin@example.com'),
                new OA\Property(property: 'password', type: 'string', example: 'admin123')
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Connexion réussie (JWT token)',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'token', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...')
            ]
        )
    )]
    #[OA\Response(
        response: 401,
        description: 'Identifiants invalides'
    )]
    public function login_check_doc(): Response
    {
        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/test-login', methods: ['POST'])]
    #[OA\Tag(name: 'Auth')]
    public function testLogin(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $hasher): JsonResponse
    {
        $email = "admin@example.com";
        $password = "adminpass";

        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur introuvable'], 404);
        }

        if (!$hasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['error' => 'Mot de passe invalide'], 401);
        }

        return new JsonResponse(['message' => 'Connexion réussie']);
    }




}
