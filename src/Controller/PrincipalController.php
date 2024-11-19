<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sejour;
use App\Form\SejourType;
use Doctrine\Persistence\ManagerRegistry;

class PrincipalController extends AbstractController
{
    #[Route('/principal', name: 'app_principal')]
    public function index(): Response
    {
        return $this->render('principal/index.html.twig', [
            'controller_name' => 'PrincipalController',
        ]);
    }
    #[Route('/infirmier', name: 'infirmier')]
    public function index2(): Response
    {
        return $this->render('infirmier/infirmier.html.twig', [
            'controller_name' => 'PrincipalController',
        ]);
    }
    #[Route('/creer', name: 'creersejour')]
     
    public function creersejour(Request $request, EntityManagerInterface $em): Response
    {
        $sejour = new Sejour();
        $sejour->setDateArrivee(new \DateTime());

        $form = $this->createForm(SejourType::class, $sejour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $lit = $sejour->getLit();


            $em->persist($sejour);
            $em->flush();

            $this->addFlash('success', 'Le début de séjour a été enregistré avec succès');
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/admin.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/sejours', name: 'sejour_liste')]
    public function sejourListe(EntityManagerInterface $em): Response
    {
        $sejours = $em->getRepository(Sejour::class)->findAll();

        return $this->render('admin/sejour_liste.html.twig', [
            'sejours' => $sejours,
        ]);
    }

    #[Route('/modifier/{id}', name: 'modifiersejour')]
public function modifiersejour(int $id, Request $request, EntityManagerInterface $em): Response
{
    // Récupérer le séjour à modifier
    $sejour = $em->getRepository(Sejour::class)->find($id);

    if (!$sejour) {
        throw $this->createNotFoundException('Séjour non trouvé');
    }

    $form = $this->createForm(SejourType::class, $sejour);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($sejour);
        $em->flush();

        $this->addFlash('success', 'Le séjour a été modifié avec succès');
        return $this->redirectToRoute('sejour_liste');
    }

    return $this->render('admin/modifiersejour.html.twig', [
        'form' => $form->createView(),
    ]);
}

    #[Route('/arrivee_patient', name: 'arrivee_patient')]
     
    public function arriverpatient(EntityManagerInterface $em): Response
    {
        $sejours = $em->getRepository(Sejour::class)->findAll();

        return $this->render('infirmier/arrivee.html.twig', [
            'sejours' => $sejours,
        ]);
    }


    #[Route('/informationsejour', name: 'informationsejour')]
    public function informationsejour(int $id, Request $request, EntityManagerInterface $em): Response
    {
        
    
        return $this->render('admin/modifiersejour.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/sortie_patient', name:'sortie_patient')]
    public function sortiepatient(ManagerRegistry $doctrine):Response
    {
        $repository = $doctrine->getRepository(Sejour::class);
        
        return $this->render('infirmier/sortie.html.twig');
    }



}
