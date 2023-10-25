<?php

namespace App\Controller;

use App\Entity\Component;
use App\Entity\DesignSystem;
use App\Form\NewDesignSystemType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/designsystems/new', name: 'app_new_design_system')]
    public function createDS(Request $request, ManagerRegistry $doctrine): Response
    {
        $ds = new DesignSystem();
        $form = $this->createForm(NewDesignSystemType::class, $ds);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $doctrine->getManager();
            $entityManager->persist($ds);
            $entityManager->flush();
            $this->addFlash('success', $ds->getName() . ' has been successfully created.');
            return $this->redirectToRoute('app_design_systems');
        }

        $this->denyAccessUnlessGranted('ROLE_CLIENT');
        return $this->render('design_systems/new.html.twig', [
            'controller_name' => 'DesignSystemsController',
            'form' => $form->createView()
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
