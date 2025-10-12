<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

final class MailerController extends AbstractController
{
    #[Route('/mailer', name: 'app_mailer')]
    public function index(): Response
    {
        return $this->render('mailer/index.html.twig', [
            'controller_name' => 'MailerController',
        ]);
    }

    #[Route('/test-mail', name: 'test_mail')]
    public function testMail(MailerInterface $mailer, Environment $twig): Response
    {
        $html = $twig->render('email/newsletter.html.twig', ['username' => 'Test']);

        $email = (new Email())
            ->from('no-reply@example.com')
            ->to('test@example.com')
            ->subject('Test Mailtrap')
            ->text('Version texte')
            ->html($html);

        $mailer->send($email);

        return new Response('Email envoyé');
    }
}
