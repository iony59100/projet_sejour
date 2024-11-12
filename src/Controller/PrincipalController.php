<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sejour;
use App\Form\SejourType;

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
    #[Route('/arrivee-patient', name: 'arrivee_patient')]
     
    public function arriveePatient(Request $request, EntityManagerInterface $em): Response
    {
        $sejour = new Sejour();
        $sejour->setDateArrivee(new \DateTime());

        $form = $this->createForm(SejourType::class, $sejour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le lit associé au séjour et changer son état
            $lit = $sejour->getLit();
            $lit->setEtat('occupé');

            // Persister les modifications
            $em->persist($sejour);
            $em->flush();

            $this->addFlash('success', 'Le début de séjour a été enregistré avec succès et le lit est maintenant occupé.');
            return $this->redirectToRoute('arrivee_patient');
        }

        return $this->render('infirmier/arrivee.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin', name: 'admin')]
    public function index3(): Response
    {
        return $this->render('admin/admin.html.twig', [
            'controller_name' => 'PrincipalController',
        ]);
    }
}
