<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Component;
use App\Entity\ComponentState;
use App\Form\ChangeComponentStateType;
use App\Form\NewTransactionType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowComponentController extends AbstractController
{
    #[Route('/component/{id}', name: 'app_show_component')]
    public function index(int $id, ManagerRegistry $doctrine, Request $request): Response
    {

        $repository = $doctrine->getRepository(Component::class);
        $component = $repository->find($id);

        if(!$component){
            $this->addFlash('success', "This component doesn't exists, you have been redirected to your dashboard.");
            return $this->redirectToRoute('app_dashboard');
        }

        $transaction = new Activity();
        $form = $this->createForm(NewTransactionType::class, $transaction);
        $form->handleRequest($request);

        $changeComponentStateForm = $this->createForm(ChangeComponentStateType::class, $component);
        $changeComponentStateForm->handleRequest($request);

        $buyer = $this->getUser();
        $seller = $component->getOwner();

        if($component->getPrice()){
            $price = $component->getPrice();
        }else{
            $price = 0;
        }

        if($changeComponentStateForm->isSubmitted()){
            $privatestate = $doctrine->getManager()->getRepository(ComponentState::class)->find(2); // Private State
            $publicstate = $doctrine->getManager()->getRepository(ComponentState::class)->find(3); // Public State
            if($component->getState() == $privatestate){
                if($component->getCode() == ""){
                    $this->addFlash('success', "You can't publish an empty component");
                    return $this->redirectToRoute('app_show_component', array('id' => $component->getId()));
                }
                $component->setState($publicstate);
                $this->addFlash('success', "Your component " . $component->getName() . " has now been published for everyone");

            }else{
                $component->setState($privatestate);
                $this->addFlash('success', "Your component " . $component->getName() . " is now private");
            }
            $entityManager = $doctrine->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('app_show_component', array('id' => $component->getId()));
        }

        if($form->isSubmitted()){

            if($buyer->getBalance() < $price){
                $this->addFlash('success', 'You don\'t have enough balance to buy ' . $component->getName() . '. Deposit to your account now!');
                return $this->redirectToRoute('app_show_component', array('id' => $component->getId()));
            }

            if($buyer == $seller){
                $this->addFlash('success', 'You can\'t buy your own components!');
                return $this->redirectToRoute('app_show_component', array('id' => $component->getId()));
            }

            $hasBoughtComponent = false;
            foreach ($buyer->getBuys() as $buy) {
                $boughtComponent = $buy->getComponent();
                if ($boughtComponent === $component) {
                    $hasBoughtComponent = true;
                    break;
                }
            }

            if($hasBoughtComponent){
                $this->addFlash('success', 'You already bought ' . $component->getName() . '. Find it in your components library!');
                return $this->redirectToRoute('app_show_component', array('id' => $component->getId()));
            }

            // Création de la transaction
            $transaction->setAmount($price);
            $transaction->setBuyer($buyer);
            $transaction->setSeller($seller);
            $transaction->setComponent($component);
            $transaction->setTransactionDate(new \DateTimeImmutable());

            // Mouvement de solde si prix > 0
            if($price > 0){
                $seller->setBalance($seller->getBalance() + $price);
                $buyer->setBalance($buyer->getBalance() - $price);
            }

            //Flush to DB
            $entityManager = $doctrine->getManager();
            $entityManager->persist($transaction);
            $entityManager->flush();

            // Redirection
            $this->addFlash('success', $component->getName() . ' is now yours, find it in your components library!');
            return $this->redirectToRoute('app_show_component', array('id' => $component->getId()));
        }

        return $this->render('show_component/index.html.twig', [
            'controller_name' => 'ShowComponentController',
            'component' => $component,
            'form' => $form->createView(),
            'stateform' => $changeComponentStateForm->createView()
        ]);
    }
}
