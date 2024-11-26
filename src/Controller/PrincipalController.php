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
use App\Entity\Patient;
use App\Form\PatientType;
use App\Form\SejourType;
use Doctrine\Persistence\ManagerRegistry;

class PrincipalController extends AbstractController
{
    #[Route('/principal', name: 'app_principal')]
    public function index(): Response
    {
        $user = $this->getUser();

        if ($user) {
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                return $this->redirectToRoute('sejour_liste');
            }

            if (in_array('ROLE_INF', $user->getRoles())) {
                return $this->redirectToRoute('arrivee_patient');
            }
        }
        return $this->render('principal/index.html.twig', [
            'controller_name' => 'PrincipalController',
        ]);
    }
    #[Route('/logout', name:'app_logout')]
public function logout(): void
{
// controller can be blank: it will never be called!
throw new \Exception('Don\'t forget to activate logout in security.yaml');
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
            return $this->redirectToRoute('sejour_liste');
        }

        return $this->render('admin/admin.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/gererpatient', name: 'gererpatient')]
    public function listepatient(EntityManagerInterface $em): Response
    {
        $patients = $em->getRepository(Patient::class)->findAll();

        return $this->render('admin/patientliste.html.twig', [
            'patients' => $patients,
        ]);
    }
    #[Route('/modifierpatient/{id}', name: 'modifierpatient')]
    public function modifierpatient(int $id, Request $request, EntityManagerInterface $em): Response
    {
    
        $patient = $em->getRepository(Patient::class)->find($id);

        if (!$patient) {
            throw $this->createNotFoundException('Séjour non trouvé');
        }

        $form = $this->createForm(PatientType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($patient);
            $em->flush();

            $this->addFlash('success', 'Le séjour a été modifié avec succès');
            return $this->redirectToRoute('gererpatient');
        }

        return $this->render('admin/modifierpatient.html.twig', [
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

        $this->addFlash('success', 'L\'arrivée du patient a été validée.');


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
    $sejours = $em->getRepository(Sejour::class)->findBy(['etat' => 1]);

        // Passer la liste des séjours au template
        return $this->render('infirmier/sortie.html.twig', [
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
    ->add('commentaire', TextType::class, [
        'required' => false,
        'label' => 'Commentaire',
        'attr' => [
            'class' => 'form-control',
           
        ]
    ])
    ->add('etat', SubmitType::class, [
        'label' => 'Valider la sortie',
        'attr' => ['class' => 'btn btn-success']
    ])
    ->getForm();


    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
       
        $sejour->setEtat(false);
        $em->flush();

        $this->addFlash('success', 'La sortie du patient a été validée.');


        return $this->redirectToRoute('liste_sejours_actuels');
    }

    return $this->render('infirmier/unpatient.html.twig', [
        'sejour' => $sejour,
        'form' => $form->createView(),
    ]);



}
}