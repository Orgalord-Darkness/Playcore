<?php 

namespace App\Service; 

use Symfony\Component\Mailer\MailerInterface;
use App\Repository\VideoGameRepository; 
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerService
{
    private MailerInterface $mailer; 
    private Environment $twig;
    private VideoGameRepository $video_game_repository;

    public function __construct(MailerInterface $mailer, Environment $twig, VideoGameRepository $video_game_repository)
    {
        $this->mailer = $mailer; 
        $this->twig = $twig; 
        $this->video_game_repository = $video_game_repository; 
    }

    public function sendEmail(string $to, string $subject, string $content,string $template): void
    {
        $videoGames = $this->video_game_repository->findBy([], ['releaseDate' => 'DESC'], 5);
        $html = $this->twig->render($template, [
            'videoGames' => $videoGames
        ]);

        
        $email = (new Email())
            ->from('newsletter@playcore.com')
            ->to($to)
            ->subject($subject)
            ->text($content)
            ->html($html);

        $this->mailer->send($email);
    }
}