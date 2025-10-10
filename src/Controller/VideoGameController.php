<?php

namespace App\Controller;

use App\Repository\VideoGameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

final class VideoGameController extends AbstractController
{
    #[Route('/video/game', name: 'app_video_game')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/VideoGameController.php',
        ]);
    }

    #[Route('/api/v1/video/game', name: 'videogames', methods: ['GET'])]
    #[OA\Tag(name: 'Video Games')]
    public function getVideoGames(VideoGameRepository $video_game_repository): JsonResponse
    {
        $videogames = $video_game_repository->findAllVideoGames();

        return $this->json([
            'videogames' => $videogames, 
            'path' => 'src/Controller/VideoGameController.php',
        ]);
    }
}
