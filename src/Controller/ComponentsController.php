<?php

namespace App\Controller;

use App\Entity\Component;
use App\Entity\ComponentState;
use App\Entity\DesignSystem;
use App\Entity\DesignSystemState;
use App\Entity\DesignSystemType;
use App\Form\CreateComponentType;
use App\Form\NewDesignSystemType;
use App\Form\UpdateComponentType;
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
    #[Route('/components/view/{id}', name: 'app_view_components')]
    public function viewComponent(int $id, Request $request, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');

        $repository = $doctrine->getRepository(Component::class);
        $component = $repository->find($id);

        if(!$component){
            $this->addFlash('success', "This component doesn't exists, you have been redirected to your dashboard.");
            return $this->redirectToRoute('app_dashboard');
        }
        return $this->render('components/view.html.twig', [
            'component' => $component
        ]);
    }

    #[Route('/components/new', name: 'app_create_components')]
    public function createComponent(Request $request, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');

        $component = new Component();
        $userDesignSystems = $this->getUser()->getDesignSystems();
        $ds = new DesignSystem();
        $formds = $this->createForm(NewDesignSystemType::class, $ds);
        $form = $this->createForm(CreateComponentType::class, $component, [
            'userDesignSystems' => $userDesignSystems,
        ]);
        $form->handleRequest($request);
        $formds->handleRequest($request);

        if($formds->isSubmitted() && $formds->isValid()){
            $entityManager = $doctrine->getManager();
            $ds->setCreatedAt(New \DateTimeImmutable());
            $ds->setOwner($this->getUser());
            $privatestate = $entityManager->getRepository(DesignSystemState::class)->find(2); // State 2 = Private
            $ds->setState($privatestate);
            $alltype = $entityManager->getRepository(DesignSystemType::class)->find(4); // State 4 = ALL
            $ds->setType($alltype);
            $entityManager->persist($ds);
            $entityManager->flush();
            $this->addFlash('success', 'Design System '. $ds->getName().' has been created, you can now add components into it!');
            return $this->redirectToRoute('app_create_components', array('id' => $component->getId()));
        }

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
        return $this->render('components/new.html.twig', [
            'form' => $form->createView(),
            'formds' => $formds->createView()
        ]);
    }

    #[Route('/components/edit/{id}', name: 'app_edit_components')]
    public function editComponent(int $id, Request $request, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');

        $repository = $doctrine->getRepository(Component::class);
        $component = $repository->find($id);

        if(!$component){
            $this->addFlash('success', "This component doesn't exists, you have been redirected to your dashboard.");
            return $this->redirectToRoute('app_dashboard');
        }

        if($component->getOwner() !== $this->getUser()){
            $this->addFlash('success', "You can't edit this component.");
            return $this->redirectToRoute('app_dashboard');
        }
        $userDesignSystems = $this->getUser()->getDesignSystems();
        $form = $this->createForm(UpdateComponentType::class, $component, [
            'userDesignSystems' => $userDesignSystems,
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $doctrine->getManager();
            $entityManager->persist($component);
            $entityManager->flush();
            $this->addFlash('success', 'Component '.$component->getName().' has been edited!');
            return $this->redirectToRoute('app_show_component', array('id' => $component->getId()));
        }
        return $this->render('components/edit.html.twig', [
            'form' => $form->createView(),
            'component' => $component
        ]);
    }

    #[Route('/components/delete/{id}', name: 'app_delete_component')]
    public function deleteComponent(int $id, Request $request, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');

        $repository = $doctrine->getRepository(Component::class);
        $component = $repository->find($id);

        if(!$component){
            $this->addFlash('success', "This component doesn't exists, you have been redirected to your dashboard.");
            return $this->redirectToRoute('app_dashboard');
        }

        if($component->getOwner() !== $this->getUser()){
            $this->addFlash('success', "You can't delete this component.");
            return $this->redirectToRoute('app_dashboard');
        }

        $entityManager = $doctrine->getManager();
        $entityManager->remove($component);
        $entityManager->flush();
        $this->addFlash('success', 'Component '.$component->getName().' has been deleted successfully.');
        return $this->redirectToRoute('app_components', array('id' => $component->getId()));
    }
}
