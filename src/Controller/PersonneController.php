<?php

namespace App\Controller;

use App\Entity\Personne;

use App\Form\PersonneType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PersonneController extends AbstractController
{
    #[Route('/personnes', name: 'personnes')]
    public function index(): Response
    {
        $repo = $this->getDoctrine()->getRepository(Personne::class);
        $personnes = $repo->findAll();
        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes
        ]);
    }

    #[Route('/personnes/new', name: 'addPersonne')]
    public function post(Request $request)
    {
        $personne = new Personne();
        $form = $this->createForm(PersonneType::class, $personne);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();

            $em->persist($personne);
            $em->flush();

            return $this->redirectToRoute('soiree');
        }

        return $this->render("personne/new.html.twig",[
            "formulaire" => $form->createView()
        ]);
    }
}
