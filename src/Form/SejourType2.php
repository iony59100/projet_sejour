<?php
namespace App\Form;

use App\Entity\Lit;
use App\Entity\Sejour;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class SejourType2 extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateArrivee', null, [
                'widget' => 'single_text',
            ])
            ->add('dateSortie', null, [
                'widget' => 'single_text',
            ])
            ->add('commentaire')
            ->add('lit', EntityType::class, [
                'class' => Lit::class,
                'choice_label' => 'id', // Utilisation de l'ID du lit
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('l')
                        ->where('l.etat = :etat') // Filtre pour l'état 'libre'
                        ->setParameter('etat', 'libre');
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sejour::class,
            'patient' => null, // Adding 'patient' as a custom option
        ]);
    }
}