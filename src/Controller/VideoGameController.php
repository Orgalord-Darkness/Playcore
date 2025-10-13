<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use App\Entity\VideoGame;
use App\Entity\Editor;
use App\EventListener\ExcpetionListener;
use App\Repository\VideoGameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
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
        ], Response::HTTP_OK, [], ['groups' => 'getVideoGame']);
    }

    #[Route('/api/v1/videogame/list', methods: ['GET'])]
    #[OA\Tag(name: 'Video Games')]
    public function listVideoGames(
        VideoGameRepository $repository, 
        Request $request, 
        TagAwareCacheInterface $cachePool
    ) {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        $cacheIdentifier = "getAllVideoGames-" . $page . "-" . $limit;

        $videogames = $cachePool->get($cacheIdentifier, 
            function (ItemInterface $item) use ($repository, $page, $limit) {
                $item->tag('videogameCache');
                return $repository->findAllWithPagination($page, $limit);
            }
        );

        return $this->json($videogames, Response::HTTP_OK, [], ['groups' => 'getVideoGame']);
    }

    #[Route('/api/v1/videogame/create', name:'add_video_game', methods: ['POST'])]
    #[OA\Tag(name: 'Video Games')]
    #[OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: "multipart/form-data",
            schema: new OA\Schema(
                type: "object",
                required: ["title", "releaseDate", "description", "editor"],
                properties: [
                    new OA\Property(property: "title", type: "string", example: "The Witcher 3"),
                    new OA\Property(property: "releaseDate", type: "string", format: "date", example: "2015-05-19"),
                    new OA\Property(property: "description", type: "string", example: "An open-world RPG game"),
                    new OA\Property(property: "coverImageFile", type: "string", format: "binary"),
                    new OA\Property(property: "editor", 
                        type: "object", 
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "name", type: "string", example:"Nintendo"),
                            new OA\Property(property: "country", type: "string", example:"Japon")
                        ]
                    )
                ]
            )
        )
    )]
    public function createVideoGame(
        Request $request,
        EntityManagerInterface $em, 
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator,
        TagAwareCacheInterface $cachePool
    ): JsonResponse {
        $videogame = new VideoGame();
        $videogame->setTitle($request->request->get('title'));
        $videogame->setDescription($request->request->get('description'));

        $releaseDate = $request->request->get('releaseDate');
        if ($releaseDate) {
            $videogame->setReleaseDate(new \DateTime($releaseDate));
        }
        
        $coverImage = $request->files->get('coverImage');
        if($coverImage){
            $newFilename = uniqid().'.'.$coverImage->guessExtension();
            try{
                $coverImage->move(
                    $this->getParameter('cover_image_directory'),
                    $newFilename
                ); 
                $videogame->setCoverImage($newFilename); 
            }catch(ExcpetionListener $e){
                return new JsonResponse(['error' => 'Impossible d\'enregistrer l\'image'], 500 );
            }
        }
        
        $editorName = $request->request->get('editor[name]');
        $editorId = $request->request->get('editor[id]');
        $editorCountry = $request->request->get('editor[country]');

        if (empty($editorName)) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Editor name is required'
            ], Response::HTTP_BAD_REQUEST);
        }

        $editor = new Editor();
        $editor->setId($editorId);
        $editor->setName($editorName);
        $editor->setCountry($editorCountry);

        $em->persist($videogame);
        $em->flush();

        $cachePool->invalidateTags(['videogameCache']);

        $location = $urlGenerator->generate(
            'add_video_game', ['id' => $videogame->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        ); 
        
        return $this->json($videogame, Response::HTTP_CREATED, ["Location" => $location], ['groups' => 'getVideoGame']);
    }

    #[Route('/api/v1/videogame/{id}', name: "update_video_game", methods: ['PUT'])]
    #[OA\Tag(name: 'Video Games')]
    #[OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: "multipart/form-data",
            schema: new OA\Schema(
                type: "object",
                required: ["title", "releaseDate", "description", "editor"],
                properties: [
                    new OA\Property(property: "title", type: "string", example: "The Witcher 3"),
                    new OA\Property(property: "releaseDate", type: "string", format: "date", example: "2015-05-19"),
                    new OA\Property(property: "description", type: "string", example: "An open-world RPG game"),
                    new OA\Property(property: "coverImageFile", type: "string", format: "binary"),
                    new OA\Property(property: "editor", 
                        type: "object", 
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "name", type: "string", example:"Nintendo"),
                            new OA\Property(property: "country", type: "string", example:"Japon")
                        ]
                    )
                ]
            )
        )
    )]
    public function updateVideoGame(
        Request $request, 
        SerializerInterface $serializer, 
        VideoGame $updatedVideoGame,
        EntityManagerInterface $em, 
        UrlGeneratorInterface $urlGenerator, 
        TagAwareCacheInterface $cachePool
    ): JsonResponse {
        $updatedVideoGame->setTitle($request->request->get('title'));
        $updatedVideoGame->setDescription($request->request->get('description'));

        $releaseDate = $request->request->get('releaseDate');
        if ($releaseDate) {
            $updatedVideoGame->setReleaseDate(new \DateTime($releaseDate));
        }

        $coverImage = $request->files->get('coverImage');
        if($coverImage){
            $newFilename = uniqid().'.'.$coverImage->guessExtension();
            try{
                $coverImage->move(
                    $this->getParameter('cover_image_directory'),
                    $newFilename
                ); 
                $updatedVideoGame->setCoverImage($newFilename); 
            }catch(ExcpetionListener $e){
                return new JsonResponse(['error' => 'Impossible d\'enregistrer l\'image'], 500 );
            }
        }

        $em->persist($updatedVideoGame);
        $em->flush();

        $cachePool->invalidateTags(['videogameCache']);

        $location = $urlGenerator->generate(
            'update_video_game', ['id' => $updatedVideoGame->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->json(['status' => 'success'], Response::HTTP_OK, ["Location" => $location]); 
    }

    #[Route('/api/v1/videogame/{id}', name: 'deleteVideoGame', methods: ['DELETE'])]
    #[OA\Tag(name: 'Video Games')]
    public function deleteVideoGame(
        VideoGame $videogame, 
        EntityManagerInterface $em, 
        TagAwareCacheInterface $cachePool
    ): JsonResponse {
        $em->remove($videogame);
        $em->flush();

        $cachePool->invalidateTags(['videogameCache']);

        return new JsonResponse(['status' => 'success'], Response::HTTP_OK);
    }

    #[Route('/newsletter/preview', name: 'newsletter_preview')]
    public function previewNewsletter(VideoGameRepository $videoGameRepository): Response
    {
        $videoGames = $videoGameRepository->findBy([], ['releaseDate' => 'DESC'], 5);
        // dd($videoGames);

        return $this->render('email/newsletter.html.twig', [
            'username' => 'Heddy',
            'videoGames' => $videoGames
        ]);
    }
}
