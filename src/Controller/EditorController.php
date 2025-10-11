<?php

namespace App\Controller;

use Symfony\Component\Security\Core\Authorization\Attribute\IsGranted;
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
use Symfony\Component\Serializer\SerializerInterface;

final class EditorController extends AbstractController
{
    #[Route('/editor', name: 'app_editor')]
    public function index(): Response
    {
        return $this->render('editor/index.html.twig', [
            'controller_name' => 'EditorController',
        ]);
    }

    #[Route('/api/v1/editors/list', methods: ['GET'])]
    #[OA\Tag(name: 'Editors')]
    public function editors(EditorRepository $repository, Request $request){

        $page = $request->get('page',1);
        $limit = $request->get('limit',3);

        $editors = $repository->findAllWithPagination($page,$limit);
        
        return $this->json($editors);
    }

    #[Route('/api/v1/editors/create', methods: ['POST'])]
    #[OA\Tag(name: 'Editors')]
    public function createEditor(
        EditorRepository $repository, 
        Request $request,
        EntityManagerInterface $em, 
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $editor = $serializer->deserialize($request->getContent(), Editor::class, 'json');
        $em->persist($serializer);
        $em->flush();

        $location= $urlGenerator->generate(
            'editor',
            ['id' => $editor->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        ); 
        
        return $this->json($editor, Response::HTTP_CREATED, 
        ["Location" => $location], ['groups' => 'getEditor']);
    }

    #[Route('/api/v1/editor/{id}', name:'deleteEditor', methods:['DELETE'])]
    //#[IsGranted('ROLE_ADMIN', message:'Vous n\'êtes pas autorisé à supprimer un élément')]
    #[OA\Tag(name: 'Editors')]
    public function deleteEditor(Editor $editor, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($editor);
        $em->flush();
        
        return new JsonResponse(['status' => 'success'], Response::HTTP_OK);
    }



}
