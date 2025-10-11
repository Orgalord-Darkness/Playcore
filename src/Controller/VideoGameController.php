<?php

namespace App\Controller;

use Symfony\Component\Security\Core\Authorization\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use App\Entity\VideoGame;
use App\Repository\VideoGameRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

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

    #[Route('/api/v1/videogame/list', methods: ['GET'])]
    #[OA\Tag(name: 'Video Games')]
    public function categories(VideoGameRepository $repository, 
    Request $request, TagAwareCacheInterface $cachePool){

        $page = $request->get('page',1);
        $limit = $request->get('limit',3);

        $cacheIdentifier = "getAllVideoGames-".$page." - ".$limit; 

        $videogames = $cachePool->get($cacheIdentifier, 
            function (ItemInterface $item) use ($repository, $page,$limit){
                $item->tag('videogameCache');
                return $repository->findAllWithPagination($page,$limit);
            }
        ); 

        return $this->json($videogames, Response::HTTP_OK,['groups' => 'getVideoGame']);
    }

    #[Route('/api/v1/videogame/create', methods: ['POST'])]
    #[OA\Tag(name: 'Video Games')]
    public function createVideoGame(
        VideoGameRepository $repository, 
        Request $request,
        EntityManagerInterface $em, 
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator,
        TagAwareCacheInterface $cachePool): JsonResponse
    {
        $videogame = $serializer->deserialize($request->getContent(), VideoGame::class, 'json');
        $em->persist($serializer);
        $em->flush();

        $cachePool->invalidateTags(['videogameCache']);

        $location= $urlGenerator->generate(
            'videogame',
            ['id' => $videogame->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        ); 
        
        return $this->json($videogame, Response::HTTP_CREATED, 
        ["Location" => $location], ['groups' => 'getVideoGame']);
    }

    #[Route('/api/v1/videogame/{id}', name:"updateVideoGame", methods:['PUT'])]
    #[OA\Tag(name: 'Video Games')]
    public function updateVideoGame(
        Request $request, SerializerInterface $serializer, VideoGame $currentVideoGame,
        EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator,TagAwareCacheInterface $cachePool): JsonResponse
    {
        $updatedVideoGame = $serializer->deserialize($request->getContent(),
            VideoGame::class, 
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentVideoGame]);
        
        $em->persist($updatedVideoGame);
        $em->flush();

        $cachePool->invalidateTags(['videogameCache']);

        $location = $urlGenerator->generate(
            'vidoegame', ['id' => $updatedVideoGame->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->json(['status' => 'success'], Response::HTTP_OK, ["Location" => $location]); 
        
    }

    #[Route('/api/v1/videogame/{id}', name:'deleteVideoGame', methods:['DELETE'])]
    //#[IsGranted('ROLE_ADMIN', message:'Vous n\'êtes pas autorisé à supprimer un élément')]
    #[OA\Tag(name: 'Video Games')]
    public function deleteVideoGame(VideoGame $videogame, EntityManagerInterface $em,TagAwareCacheInterface $cachePool): JsonResponse
    {
        $em->remove($videogame);
        $em->flush();

        $cachePool->invalidateTags(['videogameCache']);
        
        return new JsonResponse(['status' => 'success'], Response::HTTP_OK);
    }
}
