<?php

namespace App\Controller;

use App\Entity\ProfilePicture;
use App\Form\UpdateEmailType;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use App\Entity\User;
use App\Form\CompleteProfileType;
use App\Form\UpdateAboutType;
use App\Form\UpdateNameType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }


    #[Route('/settings', name: 'app_settings', methods: ['GET', 'POST'])]
    public function index(Request $request, ManagerRegistry $doctrine, MailerInterface $mailer): Response
    {


        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();

        $form = $this->createForm(CompleteProfileType::class, $user);
        $form->handleRequest($request);

        $formname = $this->createForm(UpdateNameType::class, $user);
        $formname->handleRequest($request);

        $formbio = $this->createForm(UpdateAboutType::class, $user);
        $formbio->handleRequest($request);

        $formemail = $this->createForm(UpdateEmailType::class, $user);
        $formemail->handleRequest($request);

        if($formname->isSubmitted()){
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Your name have been updated successfully!');
            return $this->redirectToRoute('app_settings');
        }

        if($formbio->isSubmitted()){
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Your bio have been updated successfully!');
            return $this->redirectToRoute('app_settings');
        }

        if($form->isSubmitted()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            if($user->getImageName() !== "" && $user->getImageName() !== null){
                $this->addFlash('success', 'Your profile picture have been updated successfully!');
            }else{
                $this->addFlash('success', 'Your profile picture have been removed successfully!');
            }

            return $this->redirectToRoute('app_settings');
        }
            if($formemail->isSubmitted() && $formemail->isValid()){
                $user->setIsVerified(false);
                $entityManager = $doctrine->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                    (new TemplatedEmail())
                        ->from(new Address('noreply@rylco.app', 'Rylco'))
                        ->to($user->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                        ->context([
                            'user' => $user
                        ])
                );
                $this->addFlash('success', 'Your email adresss have been updated successfully, check mailbox to confirm it!');
                return $this->redirectToRoute('app_settings');
        }

        return $this->render('settings/index.html.twig', [
            'controller_name' => 'SettingsController',
            'form' => $form->createView(),
            'formname' => $formname->createView(),
            'formbio' => $formbio->createView(),
            'formemail' => $formemail->createView()
        ]);
    }
}
