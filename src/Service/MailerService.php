<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use App\Repository\VideoGameRepository;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MailerService
{
    private MailerInterface $mailer;
    private Environment $twig;
    private VideoGameRepository $video_game_repository;
    private ParameterBagInterface $params;

    public function __construct(
        MailerInterface $mailer,
        Environment $twig,
        VideoGameRepository $video_game_repository,
        ParameterBagInterface $params
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->video_game_repository = $video_game_repository;
        $this->params = $params;
    }

    // public function sendEmail(string $to, string $subject, string $content, string $template): void
    // {
    //     $videoGames = $this->video_game_repository->findBy([], ['releaseDate' => 'DESC'], 5);
    //     $coverImageDir = $this->params->get('cover_image_directory');

    //     $email = (new Email())
    //         ->from('newsletter@playcore.com')
    //         ->to($to)
    //         ->subject($subject)
    //         ->text($content);

    //     $imagesCid = [];

    //     foreach ($videoGames as $videoGame) {
    //         $imageFilename = $videoGame->getCoverImage();
    //         $imagePath = $coverImageDir . DIRECTORY_SEPARATOR . $imageFilename;

    //         if (file_exists($imagePath)) {
    //             $cid = uniqid('vg_', true);
    //             $email->embedFromPath($imagePath, $cid);
    //             $imagesCid[$videoGame->getId()] = $cid;
    //         } else {
    //             $imagesCid[$videoGame->getId()] = null;
    //         }
    //     }

    //     $html = $this->twig->render($template, [
    //         'videoGames' => $videoGames,
    //         'imagesCid' => $imagesCid,
    //     ]);

    //     $email->html($html);

    //     $this->mailer->send($email);
    // }

    public function sendEmail(string $to, string $subject, string $content, string $template): void
    {
        $videoGames = $this->video_game_repository->findBy([], ['releaseDate' => 'DESC'], 5);
        $coverImageDir = $this->params->get('cover_image_directory');

        $email = (new Email())
            ->from('newsletter@playcore.com')
            ->to($to)
            ->subject($subject)
            ->text($content);

        $imagesCid = [];

        foreach ($videoGames as $videoGame) {
            $imageFilename = $videoGame->getCoverImage();
            $imagePath = $coverImageDir . DIRECTORY_SEPARATOR . $imageFilename;

            if (file_exists($imagePath)) {
                $cid = uniqid('vg_', true);
                $email->embedFromPath($imagePath, $cid);
                $imagesCid[$videoGame->getId()] = $cid;
            } else {
                $imagesCid[$videoGame->getId()] = null;
            }
        }

        $html = $this->twig->render($template, [
            'videoGames' => $videoGames,
            'imagesCid' => $imagesCid,
        ]);

        $email->html($html);

        $this->mailer->send($email);
    }

}
