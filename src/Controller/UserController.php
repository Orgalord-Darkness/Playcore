<?php

namespace App\Controller;

use Symfony\Component\Security\Core\Authorization\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use App\Entity\User;
use App\Repository\UserRepository;
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


final class UserController extends AbstractController
{
    #[Route('/api/v1/user/list', methods: ['GET'])]
    #[OA\Tag(name: 'Users')]
    public function categories(UserRepository $repository, 
    Request $request, TagAwareCacheInterface $cachePool){

        $page = $request->get('page',1);
        $limit = $request->get('limit',3);

        $cacheIdentifier = "getCategories-".$page." - ".$limit; 

        $categories = $cachePool->get($cacheIdentifier, 
            function (ItemInterface $item) use ($repository, $page,$limit){
                $item->tag('userCache');
                return $repository->findAllWithPagination($page,$limit);
            }
        ); 
        return $this->json($categories, Response::HTTP_OK,['groups' => 'getUser']);
    }

    #[Route('/api/v1/user/create', methods: ['POST'])]
    #[OA\Tag(name: 'Users')]
    public function createUser(
        UserRepository $repository, 
        Request $request,
        EntityManagerInterface $em, 
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator,
        TagAwareCacheInterface $cachePool): JsonResponse
    {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        $em->persist($serializer);
        $em->flush();

        $cachePool->invalidateTags(['userCache']);

        $location= $urlGenerator->generate(
            'user',
            ['id' => $user->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        ); 
        
        return $this->json($user, Response::HTTP_CREATED, 
        ["Location" => $location], ['groups' => 'getUser']);
    }

    #[Route('/api/v1/user/update/{id}', name:"updateUser", methods:['PUT'])]
    #[OA\Tag(name: 'Users')]
    public function updateUser(
        Request $request, SerializerInterface $serializer, User $currentUser,
        EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator, TagAwareCacheInterface $cachePool): JsonResponse
    {
        $updatedUser = $serializer->deserialize($request->getContent(),
            User::class, 
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentUser]);
        
        $em->persist($updatedUser);
        $em->flush();

        $cachePool->invalidateTags(['userCache']);

        $location = $urlGenerator->generate(
            'user', ['id' => $updatedUser->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->json(['status' => 'success'], Response::HTTP_OK, ["Location" => $location]); 
        
    }

    #[Route('/api/v1/user/{id}', name:'deleteUser', methods:['DELETE'])]
    //#[IsGranted('ROLE_ADMIN', message:'Vous n\'êtes pas autorisé à supprimer un élément')]
    #[OA\Tag(name: 'Users')]
    public function deleteUser(User $user, EntityManagerInterface $em, TagAwareCacheInterface $cachePool): JsonResponse
    {
        $em->remove($user);
        $em->flush();

        $cachePool->invalidateTags(['userCache']);
        
        return new JsonResponse(['status' => 'success'], Response::HTTP_OK);
    }
}
