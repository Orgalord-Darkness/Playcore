<?php

namespace App\MessageHandler;

use App\Message\SendNewsLetterMessage;
use App\Repository\VideoGameRepository;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Twig\Environment;

#[AsMessageHandler]
class SendNewsLetterMessageHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig,
        private VideoGameRepository $videoGameRepository,
        private UserRepository $userRepository
    ) {}

    public function __invoke(SendNewsLetterMessage $message)
    {
        $videoGames = $this->videoGameRepository->findBy([], ['releaseDate' => 'DESC'], 5);

        $html = $this->twig->render('email/newsletter.html.twig', [
            'videoGames' => $videoGames
        ]);
        // $users = $this->userRepository->findAll();
        // foreach($users as $user){
             $email = (new Email())
            ->from('newsletter@playcore.com')
            ->to('test@example.com')
            ->subject('Newsletter Gaming')
            ->text('Bonjour, voici notre dernière newsletter.')
            ->html($html);

            $this->mailer->send($email);
        // }   
    }
}
