<?php

namespace App\Controller;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use App\Entity\Editor;
use App\Repository\EditorRepository;
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

final class EditorController extends AbstractController
{
    #[Route('/editor', name: 'app_editor')]
    public function index(): Response
    {
        return $this->render('editor/index.html.twig', [
            'controller_name' => 'EditorController',
        ]);
    }

    #[Route('/api/v1/editor/list', name:"editors", methods: ['GET'])]
    #[OA\Tag(name: 'Editors')]
    #[OA\Parameter(
        name: 'page',
        in: 'query',
        description: 'Page number for pagination',
        required: false,
        schema: new OA\Schema(type: 'integer', default: 1)
    )]
    public function editors(EditorRepository $repository, 
    Request $request, TagAwareCacheInterface $cachePool){

        $page = $request->get('page',1);
        $limit = $request->get('limit',3);

        $cacheIdentifier = "getEditors-".$page." - ".$limit; 

        $editors = $cachePool->get($cacheIdentifier, 
            function (ItemInterface $item) use ($repository, $page,$limit){
                $item->tag('editorCache');
                return $repository->findAllWithPagination($page,$limit);
            }
        ); 
        return $this->json($editors, Response::HTTP_OK,['groups' => 'getEditors']);
    }


    #[Route('/api/v1/editors/create',name:"add_editor", methods: ['POST'])]
    #[OA\Tag(name: 'Editors')]
    #[IsGranted("ROLE_ADMIN")]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            required: ['name', 'country'], 
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                new OA\Property(property: 'country', type: 'string', example: 'USA')
            ]
        )
    )]
    public function createEditor(
        EditorRepository $repository, 
        Request $request,
        EntityManagerInterface $em, 
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator,
        TagAwareCacheInterface $cachePool): JsonResponse
    {
        $editor = $serializer->deserialize($request->getContent(), Editor::class, 'json');
        $em->persist($editor);
        $em->flush();

        $cachePool->invalidateTags(['editorCache']);

        $location= $urlGenerator->generate(
            'add_editor',
            ['id' => $editor->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        ); 
        
        return $this->json($editor, Response::HTTP_CREATED, 
        ["Location" => $location], ['groups' => 'getEditor']);
    }

    #[Route('/api/v1/editor/{id}', name:"update_editor", methods:['PUT'])]
    #[OA\Tag(name: 'Editors')]
    #[IsGranted("ROLE_ADMIN")]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            required: ['name', 'country'], 
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                new OA\Property(property: 'country', type: 'string', example: 'USA')
            ]
        )
    )]
    public function updateEditor(
        Request $request, SerializerInterface $serializer, Editor $currentEditor,
        EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator,
        TagAwareCacheInterface $cachePool): JsonResponse
    {
        $updatedEditor = $serializer->deserialize($request->getContent(),
            Editor::class, 
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentEditor, 'groups' => ['updateEditor']]);
        
        $em->persist($updatedEditor);
        $em->flush();
        $cachePool->invalidateTags(['editorCache']);

        $location = $urlGenerator->generate(
            'update_editor', ['id' => $updatedEditor->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->json(['status' => 'success'], Response::HTTP_OK, ["Location" => $location]); 
        
    }

    #[Route('/api/v1/editor/{id}', name:'deleteEditor', methods:['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message:'Vous n\'êtes pas autorisé à supprimer un élément')]
    #[OA\Tag(name: 'Editors')]
    public function deleteEditor(Editor $editor, EntityManagerInterface $em, TagAwareCacheInterface $cachePool): JsonResponse
    {
        $em->remove($editor);
        $em->flush();
        $cachePool->invalidateTags(['editorCache']);
        
        return new JsonResponse(['status' => 'success'], Response::HTTP_OK);
    }
}
