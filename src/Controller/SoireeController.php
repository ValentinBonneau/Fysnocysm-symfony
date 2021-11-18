<?php

namespace App\Controller;

use App\Entity\Soiree;
use App\Form\SoireeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SoireeController extends AbstractController
{
    #[Route('/', name: 'soiree')]
    public function index(): Response
    {
        $repo = $this->getDoctrine()->getRepository(Soiree::class);


        $soirees = $repo->findAll();

        return $this->render('soiree/index.html.twig', [
            'soirees' => $soirees
        ]);
    }

    #[Route('/soiree/{idSoiree}', name: 'soireeShow')]
    public function show($idSoiree): Response
    {
        $repo = $this->getDoctrine()->getRepository(Soiree::class);

        $soiree = $repo->find($idSoiree);

        return $this->render('soiree/show.html.twig', [
            'soiree' => $soiree
        ]);
    }

    #[Route('/new', name: 'soireeAdd')]
    public function post(Request $request){
        $soiree = new Soiree();

        $form = $this->createForm(SoireeType::class, $soiree);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();

            $em->persist($soiree);
            $em->flush();
            $id = $soiree->getId();

            return $this->redirectToRoute('soireeShow', ['idSoiree' => $id]);
        }

        return $this->render("soiree/new.html.twig",[
            "formulaire" => $form->createView()
        ]);
    }
}
