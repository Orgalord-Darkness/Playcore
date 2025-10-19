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
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTDecoderInterface;
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
                new OA\Property(property: 'username', type: 'string', example: 'admin'),
                new OA\Property(property: 'password', type: 'string', example: 'adminpass')
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

    #[Route('/api/test-token', methods:["GET"])]
    #[OA\Tag(name: 'Auth')]
    public function testToken(Request $request, JWTManager $jwtManager): JsonResponse
    {
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return new JsonResponse(['error' => 'No Bearer token provided'], 400);
        }

        $token = substr($authHeader, 7); // remove "Bearer "

        try {
            // parse() est une méthode disponible dans JWTManager depuis la v3
            $data = $jwtManager->parse($token);

            return new JsonResponse(['token_data' => $data]);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 401);
        }
    }

}
