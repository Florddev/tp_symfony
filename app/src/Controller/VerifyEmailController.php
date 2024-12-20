<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class VerifyEmailController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/verify/email/resend', name: 'app_verify_email_resend')]
    public function resendVerifyEmail(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if ($user->isVerified()) {
            return $this->redirectToRoute('app_home');
        }

        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address('noreply@eventmanager.com', 'Event Manager'))
                ->to($user->getEmail())
                ->subject('Confirmation de votre adresse email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );

        $this->addFlash('success', 'Un nouvel email de vérification vous a été envoyé.');

        return $this->redirectToRoute('app_verify_email');
    }

    #[Route('/verify/email/status', name: 'app_verify_email_status')]
    public function verifyEmailStatus(): Response
    {
        return $this->render('registration/verify_email.html.twig');
    }
}