<?php

namespace App\Controller;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use App\Repository\VideoGameRepository;
use Symfony\Component\Mime\Email;
use Twig\Environment;


class MailerController extends AbstractController
{
    #[Route('/mailer', name: 'app_mailer')]
    public function index(): Response
    {
        return $this->render('mailer/index.html.twig', [
            'controller_name' => 'MailerController',
        ]);
    }

    #[Route('/test-mail', name: 'test_mail')]
    public function testMail(MailerInterface $mailer, Environment $twig, VideoGameRepository $video_game_repository): Response
    {
        $videoGames = $video_game_repository->findBy([], ['releaseDate' => 'DESC'], 5);
        $html = $twig->render('email/newsletter.html.twig', [
            'videoGames' => $videoGames
        ]);


        $email = (new Email())
            ->from('no-reply@example.com')
            ->to('test@example.com')
            ->subject('Test Mailtrap')
            ->text('Version texte')
            ->html($html);

        $mailer->send($email);

        return new Response('Email envoyé');
    }

    #[Route('/test2-mail', name: 'test2_mail')]
    public function testMail2(
        MailerInterface $mailer,
        Environment $twig,
        VideoGameRepository $video_game_repository,
        ParameterBagInterface $params
    ): Response
    {$videoGames = $video_game_repository->findBy([], ['releaseDate' => 'DESC'], 7);

        $email = (new Email())
            ->from('newsletter@playcore.com')
            ->to('test@example.com')
            ->subject('Test Mailtrap')
            ->text('Version texte');

        $coverImageDir = $params->get('cover_image_directory');
        $imagesCid = [];

        foreach ($videoGames as $videoGame) {
            $imageFilename = $videoGame->getCoverImage();
            $imagePath = $coverImageDir . DIRECTORY_SEPARATOR . $imageFilename;

            if (file_exists($imagePath)) {
                $cid = uniqid('vg_', true);
                $email->embedFromPath($imagePath, $cid);
                $imagesCid[$videoGame->getId()] = $cid;
            }
        }

        $html = $twig->render('email/newsletter2.html.twig', [
            'videoGames' => $videoGames,
            'imagesCid' => $imagesCid,
        ]);

        $email->html($html);

        $mailer->send($email);
        

        return new Response('Email envoyé');
    }

}
