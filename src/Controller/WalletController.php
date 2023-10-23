<?php

namespace App\Controller;

use App\Form\DepositType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class WalletController extends AbstractController
{
    #[Route('/wallet', name: 'app_wallet')]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');

        $depositform = $this->createForm(DepositType::class);
        $depositform->handleRequest($request);

        if($depositform->isSubmitted() && $depositform->isValid()) {
            $data = $depositform->getData();
            $formamount = floatval($data['amount']) ;
            $amount = 100*$formamount;
            return $this->redirect('http://127.0.0.1:8000/deposit?amount=' . $amount);

        }

        return $this->render('wallet/index.html.twig', [
            'controller_name' => 'WalletController',
            'depositform' => $depositform->createView()
        ]);
    }
}
