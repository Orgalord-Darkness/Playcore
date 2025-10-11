<?php

namespace App\Controller;

use Symfony\Component\Security\Core\Authorization\Attribute\IsGranted;
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
    public function categories(CategoryRepository $repository, Request $request){

        $page = $request->get('page',1);
        $limit = $request->get('limit',3);

        $categories = $repository->findAllWithPagination($page,$limit);
        
        return $this->json($categories);
    }

    #[Route('/api/v1/category/create', methods: ['POST'])]
    #[OA\Tag(name: 'Categories')]
    public function createCategory(
        CategoryRepository $repository, 
        Request $request,
        EntityManagerInterface $em, 
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $category = $serializer->deserialize($request->getContent(), Category::class, 'json');
        $em->persist($serializer);
        $em->flush();

        $location= $urlGenerator->generate(
            'category',
            ['id' => $category->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        ); 
        
        return $this->json($category, Response::HTTP_CREATED, 
        ["Location" => $location], ['groups' => 'getCategory']);
    }

    #[Route('/api/v1/category/update/{id}', name:"updateCategory", methods:['PUT'])]
    #[OA\Tag(name: 'Categories')]
    public function updateCategory(
        Request $request, SerializerInterface $serializer, Category $currentCategory,
        EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $updatedCategory = $serializer->deserialize($request->getContent(),
            Category::class, 
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentCategory]);
        
        $em->persist($updatedCategory);
        $em->flush();

        $location = $urlGenerator->generate(
            'category', ['id' => $updatedCategory->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->json(['status' => 'success'], Response::HTTP_OK, ["Location" => $location]); 
        
    }

    #[Route('/api/v1/category/{id}', name:'deleteCategory', methods:['DELETE'])]
    //#[IsGranted('ROLE_ADMIN', message:'Vous n\'êtes pas autorisé à supprimer un élément')]
    #[OA\Tag(name: 'Categories')]
    public function deleteCategory(Category $category, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($category);
        $em->flush();
        
        return new JsonResponse(['status' => 'success'], Response::HTTP_OK);
    }
}
