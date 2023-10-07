<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WalletController extends AbstractController
{
    #[Route('/wallet', name: 'app_wallet')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');

        return $this->render('wallet/index.html.twig', [
            'controller_name' => 'WalletController',
        ]);
    }
}
