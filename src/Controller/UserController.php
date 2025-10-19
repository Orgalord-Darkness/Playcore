<?php

namespace App\Controller;

use Symfony\Component\Security\Http\Attribute\IsGranted;
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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserController extends AbstractController
{
    #[Route('/api/v1/user/list', methods: ['GET'])]
    #[OA\Tag(name: 'Users')]
    #[OA\Parameter(
        name: 'page',
        in: 'query',
        description: 'Page number for pagination',
        required: false,
        schema: new OA\Schema(type: 'integer', default: 1)
    )]
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

    #[Route('/api/v1/user/create', name: 'add_user',methods: ['POST'])]
    #[OA\Tag(name: 'Users')]
    #[IsGranted("ROLE_ADMIN")]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            required: ['username', 'email', 'password'],
            properties: [
                new OA\Property(property: 'username', type: 'string', example: 'test'),
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'test@example.com'),
                new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string', example: 'ROLE_USER')),
                new OA\Property(property: 'password', type: 'string', format: 'password', example: 'securePassword123'),
                new OA\Property(property: 'subcription_to_newsletter', type: 'bool', format: 'bool', example: true)
            ]
        )
    )]
    public function createUser(
        Request $request,
        EntityManagerInterface $em, 
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator,
        TagAwareCacheInterface $cachePool,
        UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json', ['groups' => ['createUser']]);

        $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);

        $data = json_decode($request->getContent(), true);

        if (array_key_exists('subcription_to_newsletter', $data)) {
            $user->setSubcription($data['subcription_to_newsletter']);
        }

        $em->persist($user);
        $em->flush();

        $cachePool->invalidateTags(['userCache']);

        $location= $urlGenerator->generate(
            'add_user',
            ['id' => $user->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        ); 
        
        return $this->json($user, Response::HTTP_CREATED, 
        ["Location" => $location], ['groups' => 'getUser']);
    }

    #[Route('/api/v1/user/update/{id}', name:"update_user", methods:['PUT'])]
    #[OA\Tag(name: 'Users')]
    #[IsGranted("ROLE_ADMIN")]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            required: ['username', 'email', 'password'],
            properties: [
                new OA\Property(property: 'username', type: 'string', example: 'test'),
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'test@example.com'),
                new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string', example: 'ROLE_USER')),
                new OA\Property(property: 'password', type: 'string', format: 'password', example: 'securePassword123'),
                new OA\Property(property: 'subcription_to_newsletter', type: 'bool', format: 'bool', example: true)
            ]
        )
    )]
    public function updateUser(
        Request $request, SerializerInterface $serializer, User $currentUser,
        EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator, 
        TagAwareCacheInterface $cachePool,UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $updatedUser = $serializer->deserialize($request->getContent(),
            User::class, 
            'json',
             [
                AbstractNormalizer::OBJECT_TO_POPULATE => $currentUser,
                'groups' => ['updateUser']
            ]);
        
        $hashedPassword = $passwordHasher->hashPassword($updatedUser, $updatedUser->getPassword());
        $updatedUser->setPassword($hashedPassword);
        
        if (array_key_exists('subcription_to_newsletter', $data)) {
            $updatedUser->setSubcription($data['subcription_to_newsletter']);
        }

        $em->persist($updatedUser);
        $em->flush();

        $cachePool->invalidateTags(['userCache']);

        $location = $urlGenerator->generate(
            'update_user', ['id' => $updatedUser->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->json(['status' => 'success'], Response::HTTP_OK, ["Location" => $location]); 
        
    }

    #[Route('/api/v1/user/{id}', name:'deleteUser', methods:['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message:'Vous n\'êtes pas autorisé à supprimer un élément')]
    #[OA\Tag(name: 'Users')]
    public function deleteUser(User $user, EntityManagerInterface $em, TagAwareCacheInterface $cachePool): JsonResponse
    {
        $em->remove($user);
        $em->flush();

        $cachePool->invalidateTags(['userCache']);
        
        return new JsonResponse(['status' => 'success'], Response::HTTP_OK);
    }
}
