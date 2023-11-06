<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TosController extends AbstractController
{
    #[Route('/tos', name: 'app_tos')]
    public function index(): Response
    {
        return $this->render('product/soon.html.twig', [
            'controller_name' => 'TosController',
        ]);
    }
}
