<?php

namespace App\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sejour;
use App\Entity\Patient;
use App\Form\PatientType;
use App\Form\SejourType;
use App\Form\SejourType2;

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

    #[Route('/creer/{id}', name: 'creersejourpatient')]
public function createSejour(Request $request, Patient $patient, EntityManagerInterface $em)
{
    $sejour = new Sejour();
    $sejour->setPatient($patient);
    $sejour->setEtat(0); 
    $form = $this->createForm(SejourType2::class, $sejour, [
        'patient' => $patient, 
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($sejour);
        $em->flush();
        return $this->redirectToRoute('sejour_liste');
    }

    return $this->render('admin/creersejourpatient.html.twig', [
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

    #[Route('/creerpatient', name: 'creerpatient')]
public function creerPatient(Request $request, EntityManagerInterface $em): Response
{
    $patient = new Patient();
    $form = $this->createForm(PatientType::class, $patient);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($patient);
        $em->flush();
        $this->addFlash('success', 'Le patient a été créé avec succès');
        return $this->redirectToRoute('gererpatient');
    }
    return $this->render('admin/creerpatient.html.twig', [
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

    #[Route('/sejour_date', name: 'sejour_date')]
    public function sejourdate(Request $request, EntityManagerInterface $em): Response
    {
        
        $date = new \DateTime();
        
        
        $form = $this->createFormBuilder()
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'data' => $date,  
                'label' => 'Sélectionner une date'
            ])
            ->getForm();
    
        
        $form->handleRequest($request);
    
       
        if ($form->isSubmitted() && $form->isValid()) {
            $date = $form->getData()['date'];
        }
    
       
        $sejours = $em->getRepository(Sejour::class)->findByDateArriveeAvantOuA($date);
    
       
        return $this->render('infirmier/sejourdate.html.twig', [
            'sejours' => $sejours,
            'form' => $form->createView(), 
        ]);
    }
    
    

    
    #[Route('/informationsejour/{id}', name: 'informationsejour')]
    public function informationsejour(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $sejour = $em->getRepository(Sejour::class)->find($id);
    
        if (!$sejour) {
            throw $this->createNotFoundException('Séjour non trouvé');
        }
        $lit = $sejour->getLit();
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
        
            if ($lit) {
                $lit->setEtat('occupé');
                $em->persist($lit);
            }
            
            $em->flush(); 
    
            $this->addFlash('success', 'L\'arrivée du patient a été validée.');
    
            return $this->redirectToRoute('arrivee_patient');
        }
    
        return $this->render('infirmier/informationsejour.html.twig', [
            'sejour' => $sejour,
            'form' => $form->createView(),
        ]);
    }

#[Route('/informationsejourdate/{id}', name: 'informationsejourdate')]
public function informationsejourdate(int $id, Request $request, EntityManagerInterface $em): Response
{
    
    $sejour = $em->getRepository(Sejour::class)->find($id);

   
    if (!$sejour) {
        throw $this->createNotFoundException('Séjour non trouvé');
    }

   
    return $this->render('infirmier/sejourdateinfo.html.twig', [
        'sejour' => $sejour,
    ]);
}



#[Route('/sortiepatients', name: 'liste_sejours_actuels')]
public function listeSejoursActuels(EntityManagerInterface $em): Response
{
    $sejours = $em->getRepository(Sejour::class)->findBy(['etat' => 1]);
    

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
    $lit = $sejour->getLit();

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

        if ($lit) {
            $lit->setEtat('libre');
            $em->persist($lit);
        }
        

        $em->flush();

        $this->addFlash('success', 'La sortie du patient a été validée.');



        return $this->redirectToRoute('liste_sejours_actuels');
    }

    return $this->render('infirmier/unpatient.html.twig', [
        'sejour' => $sejour,
        'form' => $form->createView(),
    ]);

}

    #[Route(path:'/prochainsejour', name:'prochain_sejour')]
    public function prochainsejour(EntityManagerInterface $em): Response
    {
        $today = new \DateTime();
        $sejours = $em->getRepository(Sejour::class)->findByDateAfterArrivee($today);

        return $this->render('infirmier/prochain_sejour.html.twig', [
            'sejours' => $sejours,
        ]);
    }

}
