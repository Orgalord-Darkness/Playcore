<?php 

namespace App\Service; 

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerService
{
    private MailerInterface $mailer; 
    private Environment $twig;

    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer; 
        $this->twig = $twig; 
    }

    public function sendEmail(string $to, string $subject, string $content,string $template, array $context): void
    {
        $html = $this->twig->render($template, $context);

        $email = (new Email())
            ->from('no-reply@example.com')
            ->to($to)
            ->subject($subject)
            ->text($content)
            ->html($html);

        $this->mailer->send($email);
    }


}