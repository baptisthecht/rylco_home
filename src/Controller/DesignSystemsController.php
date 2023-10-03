<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DesignSystemsController extends AbstractController
{
    #[Route('/designsystems', name: 'app_design_systems')]
    public function index(): Response
    {
        return $this->render('design_systems/index.html.twig', [
            'controller_name' => 'DesignSystemsController',
        ]);
    }
}
