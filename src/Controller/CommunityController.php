<?php

namespace App\Controller;

use App\Entity\Component;
use App\Repository\ComponentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommunityController extends AbstractController
{
    #[Route('/community', name: 'app_community')]
    public function index(ManagerRegistry $doctrine, Request $request, ComponentRepository $componentRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');

        $filterstypes = $request->get('types');
        $filtersprices = $request->get('prices');
        $components = $componentRepository->getComponents($filterstypes, $filtersprices);
        $components = array_filter(
            $components,
            function ($c){
                return $c->getState() != "Private";
            }
            );

        if($request->get('ajax')){
            return new JsonResponse([
                'content' => $this->renderView("components/_content.html.twig", [
                    'components' => $components
                ])
            ]);
        }

        return $this->render('community/index.html.twig', [
            'controller_name' => 'CommunityController',
            'components' => $components,
        ]);
    }
}
