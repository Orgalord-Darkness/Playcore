<?php

namespace App\Controller;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use App\Entity\Category;
use App\Repository\CategoryRepository;
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

final class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }

    #[Route('/api/v1/category/list', methods: ['GET'])]
    #[OA\Tag(name: 'Categories')]
    #[OA\Parameter(
        name: 'page',
        in: 'query',
        description: 'Page number for pagination',
        required: false,
        schema: new OA\Schema(type: 'integer', default: 1)
    )]
    public function categories(CategoryRepository $repository, 
    Request $request, TagAwareCacheInterface $cachePool){

        $page = $request->get('page',1);
        $limit = $request->get('limit',3);

        $cacheIdentifier = "getCategories-".$page." - ".$limit; 

        $categories = $cachePool->get($cacheIdentifier, 
            function (ItemInterface $item) use ($repository, $page,$limit){
                $item->tag('categoryCache');
                return $repository->findAllWithPagination($page,$limit);
            }
        ); 
        return $this->json($categories, Response::HTTP_OK,['groups' => 'getCategory']);
    }

    #[Route('/api/v1/category/create', name:'add_category', methods: ['POST'])]
    #[OA\Tag(name: 'Categories')]
    #[IsGranted("ROLE_ADMIN")]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            required: ['name', 'country'], 
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'RPG Test'),
            ]
        )
    )]
    public function createCategory( 
        Request $request,
        EntityManagerInterface $em, 
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator,
        TagAwareCacheInterface $cachePool): JsonResponse
    {
        $category = $serializer->deserialize($request->getContent(), Category::class, 'json');
        $em->persist($category);
        $em->flush();

        $cachePool->invalidateTags(['categoryCache']);

        $location= $urlGenerator->generate(
            'add_category',
            ['id' => $category->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        ); 
        
        return $this->json($category, Response::HTTP_CREATED, 
        ["Location" => $location], ['groups' => 'getCategory']);
    }

    #[Route('/api/v1/category/update/{id}', name:"update_category", methods:['PUT'])]
    #[OA\Tag(name: 'Categories')]
    #[IsGranted("ROLE_ADMIN")]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            required: ['name', 'country'], 
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'RPG Test'),
            ]
        )
    )]
    public function updateCategory(
        Request $request, SerializerInterface $serializer, Category $currentCategory,
        EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator, TagAwareCacheInterface $cachePool): JsonResponse
    {
        $updatedCategory = $serializer->deserialize($request->getContent(),
            Category::class, 
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentCategory]);
        
        $em->persist($updatedCategory);
        $em->flush();

        $cachePool->invalidateTags(['categoryCache']);

        $location = $urlGenerator->generate(
            'update_category', ['id' => $updatedCategory->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->json(['status' => 'success'], Response::HTTP_OK, ["Location" => $location]); 
        
    }

    #[Route('/api/v1/category/{id}', name:'deleteCategory', methods:['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message:'Vous n\'êtes pas autorisé à supprimer un élément')]
    #[OA\Tag(name: 'Categories')]
    public function deleteCategory(Category $category, EntityManagerInterface $em, TagAwareCacheInterface $cachePool): JsonResponse
    {
        $em->remove($category);
        $em->flush();

        $cachePool->invalidateTags(['categoryCache']);
        
        return new JsonResponse(['status' => 'success'], Response::HTTP_OK);
    }
}
