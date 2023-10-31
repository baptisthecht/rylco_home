<?php

namespace App\Controller;

use App\Entity\Deposit;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\NoReturn;
use Stripe\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Stripe\Event;
use Symfony\Component\Validator\Constraints\DateTime;


class DepositController extends AbstractController
{
    #[Route('/deposit', name: 'app_deposit')]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');

        $stripe = new \Stripe\StripeClient('sk_test_51MMh1tIzdrNC88efNd7bQ5DLdErvMBdaNHo6h9setjX02Gca9NTj3HOoppTIHhwVgoauWywTKKlpT28j4piBGQCU008LBznSm5');

        $checkout_session = $stripe->checkout->sessions->create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Deposit on Rylco',
                    ],
                    'unit_amount' => $request->query->get('amount'),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => 'https://www.rylco.app/confirmation_payment?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => 'https://www.rylco.app/cancelled_payment',
        ]);

       return $this->redirect($checkout_session->url, 303);

    }

    /**
     * @throws ApiErrorException
     */
    #[Route('/confirmation_payment', name: 'app_confirmation_payment')]
    public function handleWebhook(Request $request, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');

        if(!isset($_GET['session_id']) || empty($_GET['session_id'])){
            $this->addFlash('success',  'There is an error with your request.');
            return $this->redirectToRoute('app_wallet');
        }
        $checkout_session_id = $_GET['session_id'];
        $stripe = new \Stripe\StripeClient('sk_test_51MMh1tIzdrNC88efNd7bQ5DLdErvMBdaNHo6h9setjX02Gca9NTj3HOoppTIHhwVgoauWywTKKlpT28j4piBGQCU008LBznSm5');

        $session = $stripe->checkout->sessions->retrieve($checkout_session_id);
        $amount = $session->amount_total / 100;
        $payment_id = $session->id;
        $status = $session->status;
        $payment_status = $session->payment_status;
        $payment_intent = $session->payment_intent;

        if($payment_id && $status === 'complete' && $payment_status === 'paid'){

            $payment_found = $doctrine->getManager()->getRepository(Deposit::class)->findOneBy((array('paymentId' => $payment_id)));

            if($payment_found){
                $this->addFlash('success',  'Your deposit have been already processed');
                return $this->redirectToRoute('app_wallet');
            }else{
                $user = $this->getUser();

                $deposit = new Deposit();
                $deposit->setAmount($amount);
                $deposit->setCreatedAt(New \DateTimeImmutable());
                $deposit->setStatus($status);
                $deposit->setPaymentId($payment_id);
                $deposit->setPaymentIntent($payment_intent);
                $deposit->setPaymentStatus($payment_status);
                $deposit->setCustomer($user);

                $user->setBalance($user->getBalance() + $amount);

                $entitymanager = $doctrine->getManager();
                $entitymanager->persist($deposit);
                $entitymanager->flush();

                // TODO: Send a confirmation email for deposit

                $this->addFlash('success',  'Your deposit of $'. $amount .' have been successfully processed.');
                return $this->redirectToRoute('app_wallet');
            }
        }else{
            $this->addFlash('success',  'There is an error with your request.');
            return $this->redirectToRoute('app_wallet');
        }


    }

    #[NoReturn] #[Route('/cancelled_payment', name: 'app_cancelled_payment')]
    public function cancelledPayment(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');

        $this->addFlash('success',  'Your deposit have been cancelled !');
        return $this->redirectToRoute('app_wallet');
    }
}
