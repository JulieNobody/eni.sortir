<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\LieuRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut')
            ->add('duree')
            ->add('dateLimiteInscription')
            ->add('nbInscriptionsMax')
            ->add('infosSortie')
            ->add('lieu', EntityType::class,[
                'class' => Lieu::class,
                'choice_label' => 'nom',
            ])
            ->add('lieu', EntityType::class,[
                'class' => Lieu::class,
                'choice_label' => 'rue',
                'mapped'=>false,
                'attr' =>
                    ['disabled' => 'disabled']

            ])
        ;

        /*
        $builder ->get('lieu')->addEventListener(
            FormEvents::POST_SUBMIT,
            //callback, fonction exécutée à l'événement
            function (FormEvents $event){
                $form = $event->getForm();
                $form->add('rue', EntityType::class, [
                    'class' => Lieu::class,
                ]);
            }
        );*/

        //<input type="text" value="" disabled />

            /*
            ->add('rue', EntityType::class,[
                'class' => Lieu::class,
                'choice_label' => 'rue',
            ])*/

        //$builder->get('lieu')->

            /*->add('ville', VilleType::class,  array(

                'class' => Ville::class,
                //Attribut utilisé pour l'affichage
                'choice_label' => 'nom',

                //Fait une requête particulière
                'query_builder' => function (VilleRepository $er) {
                    return $er->createQueryBuilder('v')
                        ->orderBy('v.ville', 'ASC');
                }
                ))*/




            //->add('')//lieu
            //->add('')//latitude
            //->add('')//longitude


        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
