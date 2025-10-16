<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

class AuthController
{
  

    #[Route('/api/v1/login', name: 'api_login', methods: ['POST'])]
    #[OA\Tag(name: 'Auth')]

    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            required: ['email', 'password'],
            properties: [
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                new OA\Property(property: 'password', type: 'string', format: 'password', example: 'yourPassword123')
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Connexion réussie (token JWT)',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'token', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGci...')
            ]
        )
    )]
    #[OA\Response(
        response: 401,
        description: 'Échec d\'authentification (identifiants invalides)'
    )]
    public function login(Request $request, SerializerInterface $serializer): JsonResponse
    {
        $email = $request->get('email');
        $password = $request->get('password');

        $data = [
            'message' => 'Cette route est gérée automatiquement par le système de sécurité Symfony + LexikJWT.',
            'email' => $email,
            'password' => $password
        ];

        $json = $serializer->serialize($data, 'json');

        return new JsonResponse($json, Response::HTTP_UNAUTHORIZED, [], true);
    }

}
