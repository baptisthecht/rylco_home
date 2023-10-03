<?php

namespace App\Controller;

use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

class ResendEmailConfirmationController extends AbstractController
{

    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }


    #[Route('/resendemailconfirmation', name: 'app_resend_email_confirmation')]
    public function index(): Response
    {
        $user = $this->getUser();
        if($user->isVerified()){
            $this->addFlash('success', 'Your email adress is already verified!');
            return $this->redirectToRoute('app_dashboard');
        }else{
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('contact@baptisthecht.fr', 'RealCo'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
                    ->context([
                        'user' => $user
                    ])
            );
            $this->addFlash('success', 'A new confirmation email have been sent to your mailbox!');
            return $this->redirectToRoute('app_settings');
        }
    }
}
