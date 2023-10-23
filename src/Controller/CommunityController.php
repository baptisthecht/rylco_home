<?php

namespace App\Controller;

use App\Entity\Component;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommunityController extends AbstractController
{
    #[Route('/community', name: 'app_community')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');

        $components = $doctrine->getManager()->getRepository(Component::class)->findAll();


        return $this->render('community/index.html.twig', [
            'controller_name' => 'CommunityController',
            'components' => $components,
        ]);
    }
}
