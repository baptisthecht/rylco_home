<?php

namespace App\Controller;

use App\Entity\Component;
use App\Entity\DesignSystem;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DesignSystemsController extends AbstractController
{
    #[Route('/designsystems', name: 'app_design_systems')]
    public function index(): Response
    {

        $this->denyAccessUnlessGranted('ROLE_CLIENT');
        return $this->render('design_systems/index.html.twig', [
            'controller_name' => 'DesignSystemsController',
        ]);
    }

    #[Route('/designsystem/{id}', name: 'app_show_design_systems')]
    public function showDS(int $id, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');

        $repository = $doctrine->getRepository(DesignSystem::class);
        $ds = $repository->find($id);

        return $this->render('design_systems/show.html.twig', [
            'controller_name' => 'DesignSystemsController',
            'ds' => $ds
        ]);
    }
}
