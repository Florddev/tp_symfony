<?php

namespace App\Controller;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    #[Route('/test-email')]
    public function testEmail(MailerInterface $mailer, LoggerInterface $logger): Response
    {
        try {
            $email = (new Email())
                ->from('hello@example.com')
                ->to('you@example.com')
                ->subject('Test email ' . time())  // Ajout d'un timestamp pour identifier facilement
                ->text('This is a test email from Symfony sent at ' . date('Y-m-d H:i:s'));

            $mailer->send($email);
            $logger->info('Email envoyé avec succès');

            return new Response('Email sent! Check Mailpit interface at http://localhost:8025');
        } catch (\Exception $e) {
            $logger->error('Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
            return new Response('Erreur lors de l\'envoi : ' . $e->getMessage());
        }
    }
}