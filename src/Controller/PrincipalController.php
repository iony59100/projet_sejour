<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
    $sejour->setEtat(0);

    $form = $this->createForm(SejourType::class, $sejour);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
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
        $today = new \DateTime();
        $sejours = $em->getRepository(Sejour::class)->findByDateArrivee($today);

        return $this->render('infirmier/arrivee.html.twig', [
            'sejours' => $sejours,
        ]);
    }


    
    #[Route('/informationsejour/{id}', name: 'informationsejour')]
    public function informationsejour(int $id, Request $request, EntityManagerInterface $em): Response

    {
    $sejour = $em->getRepository(Sejour::class)->find($id);

    if (!$sejour) {
        throw $this->createNotFoundException('Séjour non trouvé');
    }

    $form = $this->createFormBuilder($sejour)
    ->add('commentaire', TextType::class, [
        'required' => false,
        'label' => 'Commentaire',
        'attr' => [
            'class' => 'form-control',
            'readonly' => true // le champ devient en lecture seule
        ]
    ])
    ->add('etat', SubmitType::class, [
        'label' => 'Valider l\'arrivée',
        'attr' => ['class' => 'btn btn-success']
    ])
    ->getForm();


    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
       
        $sejour->setEtat(true);
        $em->flush();

        $this->addFlash('L\'arrivée du patient a été validée.');

        return $this->redirectToRoute('arrivee_patient');
    }

    return $this->render('infirmier/informationsejour.html.twig', [
        'sejour' => $sejour,
        'form' => $form->createView(),
    ]);
}

#[Route('/sortiepatients', name: 'liste_sejours_actuels')]
public function listeSejoursActuels(EntityManagerInterface $em): Response
{

    $sejours = $em->getRepository(Sejour::class)->findBy(['etat' => false]);

    return $this->render('infirmier/arrivee.html.twig', [
        'sejours' => $sejours,
    ]);
}

#[Route('/sortiepatient/{id}', name: 'sortie_patient')]
public function sortiePatient(int $id, Request $request, EntityManagerInterface $em): Response
{
    $sejour = $em->getRepository(Sejour::class)->find($id);

    if (!$sejour) {
        throw $this->createNotFoundException('Séjour non trouvé');
    }

   
    $form = $this->createFormBuilder($sejour)
        ->add('commentaire', TextareaType::class, [
            'required' => false,
            'label' => 'Commentaire',
            'attr' => [
                'class' => 'form-control',
            ],
        ])
        ->add('etat', SubmitType::class, [
            'label' => 'Valider la sortie',
            'attr' => ['class' => 'btn btn-danger'],
        ])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $sejour->setEtat(true);
        $em->flush();


        $this->addFlash('La sortie du patient a été validée.');

        return $this->redirectToRoute('liste_sejours_actuels');
    }

    return $this->render('infirmier/sortie_patient.html.twig', [
        'sejour' => $sejour,
        'form' => $form->createView(),
    ]);
}



}
