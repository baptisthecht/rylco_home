<?php

namespace App\Controller;

use App\Entity\Component;
use App\Entity\ComponentState;
use App\Form\CreateComponentType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ComponentsController extends AbstractController
{
    #[Route('/components', name: 'app_components')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('components/index.html.twig', [
            'controller_name' => 'ComponentsController',
        ]);
    }


    #[Route('/components/new', name: 'app_create_components')]
    public function createComponent(Request $request, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');

        $component = new Component();
        $form = $this->createForm(CreateComponentType::class, $component);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $doctrine->getManager();

            $component->setCreatedAt(New \DateTimeImmutable());
            $component->setOwner($this->getUser());
            $privatestate = $entityManager->getRepository(ComponentState::class)->find(2); // State 2 = Private
            $component->setState($privatestate);

            $entityManager->persist($component);
            $entityManager->flush();
            $this->addFlash('success', 'Component '.$component->getName().' has been created, click publish button to start sharing it!');
            return $this->redirectToRoute('app_show_component', array('id' => $component->getId()));
        }

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('components/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
