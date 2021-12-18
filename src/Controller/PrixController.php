<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Entity\Prix;
use App\Entity\Soiree;
use App\Form\PrixType;
use App\Repository\PersonneRepository;
use App\Repository\PrixRepository;
use App\Repository\SoireeRepository;

use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PrixController extends AbstractController
{
    private function whereUserUnused(QueryBuilder $qb, $otherPrix){
        $qb->where("1=1");
        foreach ($otherPrix as $p){
            $qb->andWhere('p.id !='.$p->getIdPersonne()->getId());
        }
        return $qb;
    }
    #[Route('/ajouterPrix/{idSoiree}', name: 'prix')]
    public function index(Request $request, $idSoiree): Response
    {
        $otherPrix = $this->getDoctrine()->getRepository(Prix::class)->findBy(['idSoiree' => $idSoiree]);


        $prix = new Prix();
        $prix->setIdSoiree($this->getDoctrine()->getRepository(Soiree::class)->findOneBy(['id' => $idSoiree]));

        $form = $this->createFormBuilder($prix)
            ->add('idPersonne', EntityType::class, [
                'class' => Personne::class,
                'choice_label' => 'prenom',
                'label' => "Personne",
                'query_builder' => function (PersonneRepository $repository) use ($otherPrix){
                    return $this->whereUserUnused($repository->createQueryBuilder('p'),$otherPrix);
                }
                
            ])
            ->add('idSoiree', EntityType::class, [
                'class' => Soiree::class,
                'choice_label' => 'lieu',
                'disabled' => true,
                'query_builder' => function (SoireeRepository $repository) use ($idSoiree) {
                    return $repository->createQueryBuilder('s')
                        ->where('s.id=:id')
                        ->setParameter('id', $idSoiree);
                },
                'label' => "Soiree à "

            ])
            ->add('montant', MoneyType::class)
            ->add('ajouter', SubmitType::class)
            ->getForm();



        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();



            $em->persist($prix);
            $em->flush();

            return $this->redirectToRoute('soiree');
        }
        return $this->render('prix/new.html.twig', [
            "formulaire" => $form->createView()
        ]);
    }
}
