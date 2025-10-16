<?php

namespace App\MessageHandler;

use App\Message\SendNewsLetterMessage;
use App\Repository\VideoGameRepository;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Twig\Environment;
use App\Service\MailerService;

#[AsMessageHandler]
class SendNewsLetterMessageHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig,
        private VideoGameRepository $videoGameRepository,
       private MailerService $mailerService,
        private UserRepository $userRepository
    ) {}
        
    public function __invoke(SendNewsLetterMessage $message)
    {
        $users = $this->userRepository->findUsersBySubcription();

        foreach ($users as $user) {
            $this->mailerService->sendEmail($user->getEmail(),'Next release games','Version texte','email/newsletter2.html.twig');
            sleep(10);        
        }
    }
}
