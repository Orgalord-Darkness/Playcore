<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use AppBundle\Entity\Reward;
use App\Entity\User;
use Nelmio\ApiDocBundle\Attribute\Model;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OA;

final class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    #[Route('/api/{user}/rewards', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of a user',
        content: new OA\JsonContent(
            type: 'array',
            // items: new OA\Items(ref: new Model(type: User::class, groups: ['full']))
        )
    )]
    #[OA\Parameter(
        name: 'order',
        in: 'query',
        description: 'The field used to order rewards',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'rewards')]
    #[Security(name: 'Bearer')]
    public function fetchUserRewardsAction(User $user)
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }
}
